<?php

namespace GlueAgency\ElasticAppSearch\services;

use Elastic\EnterpriseSearch\AppSearch\Request\CreateEngine;
use Elastic\EnterpriseSearch\AppSearch\Request\DeleteEngine;
use Elastic\EnterpriseSearch\AppSearch\Request\GetEngine;
use Elastic\EnterpriseSearch\AppSearch\Request\ListEngines;
use Elastic\EnterpriseSearch\AppSearch\Schema\Engine;
use Elastic\EnterpriseSearch\Client;
use Elastic\EnterpriseSearch\Exception\ClientErrorResponseException;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\helpers\EngineHelper;
use GlueAgency\ElasticAppSearch\presenters\EnginePresenter;
use GlueAgency\ElasticAppSearch\presenters\Presenter;
use yii\base\Component;

class EngineService extends Component
{

    protected ?Client $client = null;

    public function init()
    {
        $this->client = ElasticAppSearch::getInstance()->client->get();
    }

    public function all(): array
    {
        $data = $this->client->appSearch()
            ->listEngines(
                new ListEngines()
            )
            ->asArray();

        return $data;
    }

    public function findByName(string $name): ?EnginePresenter
    {
        try {
            $data = $this->client->appSearch()
                ->getEngine(
                    new GetEngine($name)
                )
                ->asArray();

            return new EnginePresenter($data);
        } catch(ClientErrorResponseException $e) {
            if($e->getResponse()->getStatusCode() === 404) {
                return null;
            }

            // @todo something with this
            dd($e);
        }
    }

    public function create(string|Engine $engine): EnginePresenter
    {
        $data = $this->client->appSearch()
            ->createEngine(
                new CreateEngine(EngineHelper::asEngine($engine))
            )
            ->asArray();

        return new EnginePresenter($data);
    }

    public function exists(string|Engine $engine): bool
    {
        return $this->findByName($engine) instanceof Presenter;
    }

    public function delete(string|Engine $engine): bool
    {
        return $this->client->appSearch()
            ->deleteEngine(
                new DeleteEngine(EngineHelper::asString($engine))
            )
            ->asBool();
    }
}
