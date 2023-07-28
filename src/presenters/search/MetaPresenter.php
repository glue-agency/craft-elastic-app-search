<?php

namespace GlueAgency\ElasticAppSearch\presenters\search;

use GlueAgency\ElasticAppSearch\presenters\BasePresenter;
use GlueAgency\ElasticAppSearch\presenters\PagePresenter;

class MetaPresenter extends BasePresenter
{

    public array $alerts;

    public array $warnings;

    public int $precision;

    public PagePresenter $page;

    public array $engine;

    public string $request_id;

    public function configurePage($value): void
    {
        $this->page = new PagePresenter($value);
    }
}
