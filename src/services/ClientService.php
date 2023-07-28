<?php

namespace GlueAgency\ElasticAppSearch\services;

use Elastic\EnterpriseSearch\AppSearch\Endpoints;
use Elastic\EnterpriseSearch\Client;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GuzzleHttp\Client as GuzzleClient;
use yii\base\Component;

class ClientService extends Component
{

    public function get(): Client
    {
        return new Client([
            'client'     => new GuzzleClient,
            'host'       => ElasticAppSearch::getInstance()->settings->endpoint . ':' . ElasticAppSearch::getInstance()->settings->port,
            'app-search' => [
                'apiKey' => ElasticAppSearch::getInstance()->settings->apiKey,
            ],
        ]);
    }

    public function appSearch(): Endpoints
    {
        return $this->get()->appSearch();
    }
}
