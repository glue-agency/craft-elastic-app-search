<?php

namespace GlueAgency\ElasticAppSearch\presenters\search;

use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\helpers\StringHelper;
use GlueAgency\ElasticAppSearch\presenters\BasePresenter;
use GlueAgency\ElasticAppSearch\presenters\search\fields\BaseFieldPresenter;
use GlueAgency\ElasticAppSearch\presenters\search\fields\DateFieldPresenter;
use GlueAgency\ElasticAppSearch\presenters\search\fields\MetaFieldPresenter;

class ResultPresenter extends BasePresenter
{

    public BaseFieldPresenter $id;

    public BaseFieldPresenter $section_id;

    public BaseFieldPresenter $section_handle;

    public BaseFieldPresenter $type_id;

    public BaseFieldPresenter $type_handle;

    public BaseFieldPresenter $site_id;

    public BaseFieldPresenter $site_language;

    public BaseFieldPresenter $title;

    public BaseFieldPresenter $slug;

    public DateFieldPresenter $date_updated;

    public DateFieldPresenter $date_created;

    public MetaFieldPresenter $_meta;

    public EntryQuery $entry;

    protected function initializeAttributes(array $attributes): void
    {
        foreach($attributes as $key => $values) {
            if(method_exists($this, $method = 'configure' . StringHelper::upperCamelize($key))) {
                $this->{$method}($values);

                continue;
            }

            $this->{$key} = new BaseFieldPresenter($values);
        }

        // Prepare the query for the Craft Entry
        $this->entry = Entry::find()->id($this->id->raw);
    }

    protected function configureDateUpdated($value): void
    {
        $this->date_updated = new DateFieldPresenter($value);
    }

    protected function configureDateCreated($value): void
    {
        $this->date_created = new DateFieldPresenter($value);
    }

    protected function configureMeta($value): void
    {
        $this->_meta = new MetaFieldPresenter($value);
    }
}
