<?php

namespace GlueAgency\ElasticAppSearch\services;

use craft\elements\Entry;
use Elastic\EnterpriseSearch\AppSearch\Request\DeleteDocuments;
use Elastic\EnterpriseSearch\AppSearch\Request\GetDocuments;
use Elastic\EnterpriseSearch\AppSearch\Request\IndexDocuments;
use Elastic\EnterpriseSearch\AppSearch\Request\ListDocuments;
use Elastic\EnterpriseSearch\AppSearch\Request\Search;
use Elastic\EnterpriseSearch\AppSearch\Schema\SearchRequestParams;
use Elastic\EnterpriseSearch\Client;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\formatters\EntryElementFormatter;
use GlueAgency\ElasticAppSearch\helpers\EngineHelper;
use GlueAgency\ElasticAppSearch\presenters\DocumentPresenter;
use GlueAgency\ElasticAppSearch\presenters\EnginePresenter;
use GlueAgency\ElasticAppSearch\presenters\PaginatedDocumentsPresenter;
use yii\base\Arrayable;
use yii\base\Component;

class DocumentService extends Component
{

    protected ?Client $client = null;

    public function init()
    {
        $this->client = ElasticAppSearch::getInstance()->client->get();
    }

    public function all(string|EnginePresenter $engine): PaginatedDocumentsPresenter
    {
        $data = $this->client->appSearch()
            ->listDocuments(
                new ListDocuments(EngineHelper::asString($engine))
            )
            ->asArray();

        return new PaginatedDocumentsPresenter($data);
    }

    public function search(string|EnginePresenter $engine, $query)
    {
        $data = $this->client->appSearch()
            ->search(
                new Search(EngineHelper::asString($engine), new SearchRequestParams($query))
            )
            ->asArray();

        dd($data);

        return new PaginatedDocumentsPresenter($data);
    }

    public function findById(string|EnginePresenter $engine, int $id): ?DocumentPresenter
    {
        $data = $this->client->appSearch()
            ->getDocuments(
                new GetDocuments(EngineHelper::asString($engine), [$id])
            )
            ->asArray();

        if($first = reset($data)) {
            return new DocumentPresenter($first);
        }

        return null;
    }

    public function index(string|EnginePresenter $engine, mixed $data): bool
    {
        $success = $this->client->appSearch()
            ->indexDocuments(
                new IndexDocuments(EngineHelper::asString($engine), $this->formatData($data))
            );

        return $success->asBool();
    }

    public function deleteById(string|EnginePresenter $engine, int $id): bool
    {
        return $this->deleteByIds($engine, [$id]);
    }

    public function deleteByIds(string|EnginePresenter $engine, array $ids): bool
    {
        $success = $this->client->appSearch()
            ->deleteDocuments(
                new DeleteDocuments(EngineHelper::asString($engine), $ids)
            );

        return $success->asBool();
    }


    public function delete(string|EnginePresenter $engine, Entry $entry): bool
    {
        return $this->deleteById($engine, $entry->id);
    }

    protected function formatData(mixed $data): array
    {
        // Assume it is an array of items
        if(is_array($data)) {
            return array_map(function($item) {
                return $this->formatData($item);
            }, $data);
        }

        // Process the data through their
        // respective formatter
        if($data instanceof Entry) {
            return EntryElementFormatter::format($data);
        }

        if($data instanceof Arrayable) {
            return $data->toArray();
        }

        return $data;
    }
}
