<?php

namespace GlueAgency\ElasticAppSearch\factories;

use craft\base\Element;
use craft\base\Field;
use craft\fields\Assets as AssetsField;
use craft\fields\Date as DateField;
use craft\fields\Entries as EntriesField;
use craft\fields\Matrix as MatrixField;
use GlueAgency\ElasticAppSearch\mappers\elements\fields\AssetsFieldMapper;
use GlueAgency\ElasticAppSearch\mappers\elements\fields\DateFieldMapper;
use GlueAgency\ElasticAppSearch\mappers\elements\fields\EntriesFieldMapper;
use GlueAgency\ElasticAppSearch\mappers\elements\fields\MatrixFieldMapper;
use GlueAgency\ElasticAppSearch\mappers\elements\fields\SuperTableFieldMapper;
use verbb\supertable\fields\SuperTableField;

class FieldMappingFactory
{

    protected array $mappings = [
        EntriesField::class    => EntriesFieldMapper::class,
        AssetsField::class     => AssetsFieldMapper::class,
        MatrixField::class     => MatrixFieldMapper::class,
        SuperTableField::class => SuperTableFieldMapper::class,
        DateField::class       => DateFieldMapper::class,
    ];

    public function format(Element $element, Field $field): mixed
    {
        if(in_array($field::class, array_keys($this->mappings))) {
            $class = new $this->mappings[$field::class];

            return $class->format($element, $field);
        }

        return $field->serializeValue($element->getFieldValue($field->handle), $element);
    }
}
