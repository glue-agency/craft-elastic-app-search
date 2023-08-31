<?php

namespace GlueAgency\ElasticAppSearch\queue\jobs\utility;

use Craft;
use craft\base\Element;
use craft\db\Query;
use craft\elements\Entry;
use craft\queue\BaseJob;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\helpers\EngineHelper;

class IndexEntryTypesJob extends BaseJob
{

    public array $entryHandles = [];

    public int $siteId;

    protected int $batchSize = 50;

    protected int $offset = 0;

    public function execute($queue): void
    {
        $site = Craft::$app->sites
            ->getSiteById($this->siteId);
        $engineName = ElasticAppSearch::getInstance()->getSettings()->getIndexNameBySite($site);
        $entryQuery = Entry::find()
            ->site($site)
            ->status(null)
            ->type($this->entryHandles)
            ->orderby('id');
        $entryCount = $entryQuery->count();

        while($data = $entryQuery->offset($this->offset)->limit($this->batchSize)->all()) {
            // @todo catch indexing exception
            ElasticAppSearch::getInstance()->documents->index($engineName, $data);

            $this->offset += $this->batchSize;

            $this->setProgress($queue, $this->offset / $entryCount);
        }
    }

    protected function defaultDescription(): ?string
    {
        $site = Craft::$app->sites->getSiteById($this->siteId);

        return Craft::t('elastic-app-search', 'Reindexing Entry Types ({lang}) to Elastic App Search', [
            'lang' => $site->language
        ]);
    }
}
