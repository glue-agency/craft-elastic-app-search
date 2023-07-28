<?php

namespace GlueAgency\ElasticAppSearch\queue\jobs\utility;

use Craft;
use craft\queue\BaseJob;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;

class RemoveAllEntriesJob extends BaseJob
{

    public int $siteId;

    protected ?int $total = null;

    public function execute($queue): void
    {
        $site = Craft::$app->sites->getSiteById($this->siteId);
        $engineName = ElasticAppSearch::getInstance()->getSettings()->getIndexNameBySite($site);

        do {
            // Weird, but we should not be paginating because
            // we delete documents before fetching the next page
            // resulting in the new non paginated page being all
            // new documents
            $page = ElasticAppSearch::getInstance()->documents->all($engineName);

            // Only use the first page to keep track of total
            // amount of pages because of the above behaviour
            if(! $this->total) {
                $this->total = $page->meta->page->total_pages;
            }

            if($page->hasNoResults()) {
                return;
            }

            ElasticAppSearch::getInstance()->documents->deleteByIds($engineName, array_map(function($document) {
                return $document->id;
            }, $page->results));

            $this->setProgress($queue, ($page->meta->page->current / $this->total));
        } while($page->hasNext());
    }

    protected function defaultDescription(): ?string
    {
        $site = Craft::$app->sites->getSiteById($this->siteId);

        return Craft::t('elastic-app-search', 'Removing all ({lang}) Entries from Elastic App Search', [
            'lang' => $site->language,
        ]);
    }
}
