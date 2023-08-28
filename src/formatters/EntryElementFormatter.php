<?php

namespace GlueAgency\ElasticAppSearch\formatters;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use craft\elements\Entry;
use craft\helpers\StringHelper;
use GlueAgency\ElasticAppSearch\factories\FieldFormatterFactory;

class EntryElementFormatter implements ElementFormatterInterface
{

    public static function format(Entry $entry): mixed
    {
        $data = [
            'id'             => $entry->id,
            'title'          => $entry->title,
            'slug'           => $entry->slug,
            'url'            => $entry->getUrl(),
            'enabled'            => $entry->getEnabledForSite(),
            'site_id'        => $entry->site->id,
            'site_language'  => $entry->site->language,
            'section_id'     => $entry->section->id,
            'section_handle' => $entry->section->handle,
            'type_id'        => $entry->type->id,
            'type_handle'    => $entry->type->handle,
            'post_date'   => $entry->postDate?->format('c'),
            'no_post_date'   => ! $entry->postDate,
            'expiry_date'   => $entry->expiryDate?->format('c'),
            'no_expiry_date'   => ! $entry->expiryDate,
            'date_updated'   => $entry->dateUpdated->format('c'),
            'date_created'   => $entry->dateCreated->format('c'),
        ];

        $searchableCustomFields = array_filter($entry->getFieldLayout()->getCustomFields(), function($field) {
            return $field->searchable;
        });

        $fieldFormatterFactory = new FieldFormatterFactory;
        foreach($searchableCustomFields as $field) {
            $handle = StringHelper::toSnakeCase($field->handle);
            $data[$handle] = $fieldFormatterFactory->format($entry, $field);
        }

        return $data;
    }
}
