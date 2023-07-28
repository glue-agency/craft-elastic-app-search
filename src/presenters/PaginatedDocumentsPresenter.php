<?php

namespace GlueAgency\ElasticAppSearch\presenters;

class PaginatedDocumentsPresenter extends BasePresenter
{

    public MetaPresenter $meta;

    public array $results;

    public function hasNext(): bool
    {
        return $this->meta->page->current < $this->meta->page->total_pages;
    }

    public function hasResults(): bool
    {
        return count($this->results) > 0;
    }

    public function hasNoResults(): bool
    {
        return ! $this->hasResults();
    }

    protected function configureMeta($value)
    {
        $this->meta = new MetaPresenter($value);
    }

    protected function configureResults($value)
    {
        $this->results = array_map(function($result) {
            return new DocumentPresenter($result);
        }, $value);
    }
}
