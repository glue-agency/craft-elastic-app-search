<?php

namespace GlueAgency\ElasticAppSearch\queue\jobs\entry;

use Craft;
use craft\elements\Entry;
use craft\queue\BaseJob;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;

class IndexEntryJob extends BaseJob
{

    public int $entryId;

    public int $siteId;

    public function execute($queue): void
    {
        $entry = Entry::find()
            ->id($this->entryId)
            ->siteId($this->siteId)
            ->status(null)
            ->one();
        $site = Craft::$app->sites
            ->getSiteById($this->siteId);

        ElasticAppSearch::getInstance()->documents->index(
            ElasticAppSearch::getInstance()->getSettings()->getIndexNameBySite($site),
            $entry
        );

        $this->setProgress($queue, 100);
    }

    protected function defaultDescription(): ?string
    {
        return Craft::t('elastic-app-search', 'Indexing Entry #{id} to Elastic App Search', [
            'id' => $this->entryId,
        ]);
    }
}
