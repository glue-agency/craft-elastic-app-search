<?php

namespace GlueAgency\ElasticAppSearch\helpers;

use Cake\Utility\Hash;
use Craft;
use Elastic\EnterpriseSearch\AppSearch\Schema\PaginationResponseObject;
use Elastic\EnterpriseSearch\AppSearch\Schema\SearchFields;
use Elastic\EnterpriseSearch\AppSearch\Schema\SearchRequestParams;
use Elastic\EnterpriseSearch\AppSearch\Schema\SimpleObject;
use Yii;

class SearchOptionsHelper
{

    public string $query;

    public string $site;

    public array $sections;

    public array $types;

    public array $search;

    public array $highlight;

    public int $page;

    public int $size;

    public function __construct(array $options)
    {
        $this->site = Hash::get($options, 'site', Craft::$app->sites->getCurrentSite()->handle);
        $this->query = Hash::get($options, 'query');
        $this->types = Hash::extract($options, 'types');
        $this->sections = Hash::extract($options, 'sections');
        $this->search = Hash::extract($options, 'search');
        $this->highlight = Hash::extract($options, 'highlight');
        $this->page = Hash::get($options, 'page', 1);
        $this->size = Hash::get($options, 'size', 10);
    }

    public function toSearchRequestParams(): SearchRequestParams
    {
        $searchRequestParams = new SearchRequestParams($this->query);
        $searchRequestParams->filters = new SimpleObject;

        $searchRequestParams->page = Yii::configure(new PaginationResponseObject, [
            'current' => $this->page,
            'size' => $this->size,
        ]);

        if(! empty($this->types)) {
            $searchRequestParams->filters->type_handle = $this->types;
        }

        if(! empty($this->sections)) {
            $searchRequestParams->filters->section_handle = $this->sections;
        }

        if(! empty($this->search)) {
            $searchRequestParams->search_fields = new SearchFields;

            foreach($this->search as $field) {
                $searchRequestParams->search_fields->{$field} = new SimpleObject;
            }
        }

        if(! empty($this->highlight)) {
            $searchRequestParams->result_fields = new SimpleObject;

            foreach($this->highlight as $field) {
                $searchRequestParams->result_fields->{$field} = Yii::configure(new SimpleObject, [
                    'raw' => new SimpleObject,
                    'snippet' => Yii::configure(new SimpleObject, [
                        'size' => 100,
                    ]),
                ]);
            }
        }

//        dd($searchRequestParams);

        return $searchRequestParams;
    }
}
