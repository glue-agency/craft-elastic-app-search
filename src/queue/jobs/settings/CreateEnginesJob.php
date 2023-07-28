<?php

namespace GlueAgency\ElasticAppSearch\queue\jobs\settings;

use Craft;
use craft\queue\BaseJob;
use Elastic\EnterpriseSearch\AppSearch\Schema\Engine;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;

class CreateEnginesJob extends BaseJob
{

    public array $sites = [];

    protected int $count = 0;

    protected ?int $total = null;

    public function init(): void
    {
        parent::init();

        $this->total = count($this->sites);
    }

    public function execute($queue): void
    {
        foreach($this->sites as $handle => $data) {

            // Only create new engines
            if(! ElasticAppSearch::getInstance()->engines->findByName($data['index'])) {
                $site = Craft::$app->getSites()->getSiteByHandle($handle);

                $engine = new Engine($data['index']);
                $engine->language = $site->language;

                ElasticAppSearch::getInstance()->engines->create($engine);
            }

            $this->count++;
            $this->setProgress($queue, ($this->count / $this->total));
        }
    }

    protected function defaultDescription(): ?string
    {
        return Craft::t('elastic-app-search', 'Creating Elastic App Search Engines');
    }
}
