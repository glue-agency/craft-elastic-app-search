<?php

namespace GlueAgency\ElasticAppSearch\services;

use Elastic\EnterpriseSearch\AppSearch\Request\GetSchema;
use Elastic\EnterpriseSearch\AppSearch\Request\PutSchema;
use Elastic\EnterpriseSearch\AppSearch\Schema\Engine;
use Elastic\EnterpriseSearch\Client;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\helpers\EngineHelper;
use GlueAgency\ElasticAppSearch\mappers\schemas\EntrySchema;
use GlueAgency\ElasticAppSearch\presenters\SchemaPresenter;
use yii\base\Component;

class SchemaService extends Component
{

    protected ?Client $client = null;

    public function init()
    {
        $this->client = ElasticAppSearch::getInstance()->client->get();
    }

    public function all(string|Engine $engine): SchemaPresenter
    {
        $data = $this->client->appSearch()
            ->getSchema(
                new GetSchema(EngineHelper::asString($engine))
            )
            ->asArray();

        return new SchemaPresenter($data);
    }

    public function update(string|Engine $engine): bool
    {
        $schema = (new EntrySchema)->build();

        $this->client->appSearch()
            ->putSchema(
                new PutSchema(EngineHelper::asString($engine), $schema)
            )
            ->asArray();

        return true;
    }
}
