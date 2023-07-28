<?php

namespace GlueAgency\ElasticAppSearch\services;

use Elastic\EnterpriseSearch\AppSearch\Request\Search;
use Elastic\EnterpriseSearch\AppSearch\Request\SearchEsSearch;
use Elastic\EnterpriseSearch\AppSearch\Schema\EsSearchParams;
use Elastic\EnterpriseSearch\Client;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\helpers\SearchOptionsHelper;
use GlueAgency\ElasticAppSearch\presenters\EnginePresenter;
use GlueAgency\ElasticAppSearch\presenters\search\ResultPresenter;
use GlueAgency\ElasticAppSearch\presenters\search\SearchPresenter;
use yii\base\Component;

class SearchService extends Component
{

    protected ?Client $client = null;

    public function init()
    {
        $this->client = ElasticAppSearch::getInstance()->client->get();
    }

    public function search(string|EnginePresenter $engine, SearchOptionsHelper $options): SearchPresenter
    {
        if($engine instanceof EnginePresenter) {
            $engine = $engine->name;
        }

        $data = $this->client->appSearch()
            ->search(
                new Search($engine, $options->toSearchRequestParams())
            )
            ->asArray();

        return new SearchPresenter($data);
    }

    public function esSearch(string|EnginePresenter $engine, string $query): array
    {
        if($engine instanceof EnginePresenter) {
            $engine = $engine->name;
        }

        $params = new EsSearchParams;

        $data = $this->client->appSearch()
            ->searchEsSearch(
                new SearchEsSearch($engine, ElasticAppSearch::getInstance()->settings->apiKey, $params)
            )
            ->asArray();

        return array_map(function($item) {
            return new ResultPresenter($item);
        }, $data['results']);
    }
}
