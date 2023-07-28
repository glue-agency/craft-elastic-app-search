<?php

namespace GlueAgency\ElasticAppSearch\variables;

use Craft;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\helpers\SearchOptionsHelper;
use GlueAgency\ElasticAppSearch\presenters\search\SearchPresenter;

class ElasticAppSearchVariable
{

    public function search(array $options): SearchPresenter
    {
        $searchOptions = new SearchOptionsHelper($options);

        return ElasticAppSearch::getInstance()->search->search(
            ElasticAppSearch::getInstance()->settings->getIndexNameBySiteHandle($searchOptions->site),
            $searchOptions
        );
    }
}
