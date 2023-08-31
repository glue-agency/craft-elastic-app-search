<?php

namespace GlueAgency\ElasticAppSearch\queue\jobs\utility;

use Craft;
use craft\base\Element;
use craft\db\Query;
use craft\elements\Entry;
use craft\queue\BaseJob;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\helpers\EngineHelper;

class UpdateSchemaJob extends BaseJob
{

    public int $siteId;

    public function execute($queue): void
    {
        $site = Craft::$app->sites->getSiteById($this->siteId);
        $engineName = ElasticAppSearch::getInstance()->getSettings()->getIndexNameBySite($site);

        ElasticAppSearch::getInstance()->schema->update($engineName);
    }

    protected function defaultDescription(): ?string
    {
        $site = Craft::$app->sites->getSiteById($this->siteId);

        return Craft::t('elastic-app-search', 'Updating ({lang}) Schema in Elastic App Search', [
            'lang' => $site->language
        ]);
    }
}
