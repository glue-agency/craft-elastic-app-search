<?php

namespace GlueAgency\ElasticAppSearch\presenters;

class MetaPresenter extends BasePresenter
{

    public PagePresenter $page;

    public function configurePage($value)
    {
        $this->page = new PagePresenter($value);
    }
}
