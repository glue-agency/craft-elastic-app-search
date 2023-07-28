<?php

namespace GlueAgency\ElasticAppSearch\presenters\search;

use GlueAgency\ElasticAppSearch\presenters\BasePresenter;

class SearchPresenter extends BasePresenter
{

    public array $results;

    public MetaPresenter $meta;

    public function configureResults($value)
    {
        $this->results = array_map(function($result) {
            return new ResultPresenter($result);
        }, $value);
    }

    public function configureMeta($value)
    {
        $this->meta = new MetaPresenter($value);
    }
}
