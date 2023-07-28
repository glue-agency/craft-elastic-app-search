<?php

namespace GlueAgency\ElasticAppSearch\queue\jobs\entry;

use Craft;
use craft\queue\BaseJob;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;

class DeleteEntryJob extends BaseJob
{

    public int $entryId;

    public int $siteId;

    public function execute($queue): void
    {
        $site = Craft::$app->sites
            ->getSiteById($this->siteId);

        ElasticAppSearch::getInstance()->documents->deleteById(
            ElasticAppSearch::getInstance()->getSettings()->getIndexNameBySite($site),
            $this->entryId
        );

        $this->setProgress($queue, 100);
    }

    protected function defaultDescription(): ?string
    {
        return Craft::t('elastic-app-search', 'Deleting Entry #{id} from Elastic App Search', [
            'id' => $this->entryId,
        ]);
    }
}
