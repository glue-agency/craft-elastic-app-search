<?php

namespace GlueAgency\ElasticAppSearch\presenters;

class DocumentPresenter extends BasePresenter
{

    public int $id;

    public string $title;

    public string $slug;

    public SectionPresenter $section;

    public SitePresenter $site;

    protected function configureSection($value)
    {
        $this->section = new SectionPresenter(json_decode($value, true));
    }

    protected function configureSite($value)
    {
        $this->site = new SitePresenter(json_decode($value, true));
    }
}
