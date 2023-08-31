<?php

namespace GlueAgency\ElasticAppSearch\mappers\elements;

use craft\base\FieldInterface;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;
use GlueAgency\ElasticAppSearch\factories\FieldMappingFactory;
use GlueAgency\ElasticAppSearch\helpers\FieldHelper;

class EntryMapper implements ElementMapperInterface
{

    public static function format(Entry $entry): mixed
    {
        $data = [
            'id'             => $entry->id,
            'title'          => $entry->title,
            'slug'           => $entry->slug,
            'url'            => $entry->getUrl(),
            'enabled'        => $entry->getEnabledForSite(),
            'site_id'        => $entry->site->id,
            'site_language'  => $entry->site->language,
            'section_id'     => $entry->section->id,
            'section_handle' => $entry->section->handle,
            'type_id'        => $entry->type->id,
            'type_handle'    => $entry->type->handle,
            'post_date'      => $entry->postDate?->format('c'),
            'no_post_date'   => ! $entry->postDate,
            'expiry_date'    => $entry->expiryDate?->format('c'),
            'no_expiry_date' => ! $entry->expiryDate,
            'date_updated'   => $entry->dateUpdated->format('c'),
            'date_created'   => $entry->dateCreated->format('c'),
        ];

        $searchableCustomFields = ArrayHelper::where($entry->getFieldLayout()->getCustomFields(), function(FieldInterface $field) {
            return $field->searchable;
        }, true, true, false);

        $fieldFormatterFactory = new FieldMappingFactory;
        foreach($searchableCustomFields as $field) {
            $handle = FieldHelper::toElasticSafeName($field);
            $data[$handle] = $fieldFormatterFactory->format($entry, $field);
        }

        return $data;
    }
}
