<?php

namespace GlueAgency\ElasticAppSearch\factories;

use craft\base\Element;
use craft\base\Field;
use craft\fields\Assets as AssetsField;
use craft\fields\Entries as EntriesField;
use craft\fields\Matrix as MatrixField;
use GlueAgency\ElasticAppSearch\formatters\fields\AssetsFieldFormatter;
use GlueAgency\ElasticAppSearch\formatters\fields\EntriesFieldFormatter;
use GlueAgency\ElasticAppSearch\formatters\fields\MatrixFieldFormatter;
use GlueAgency\ElasticAppSearch\formatters\fields\SuperTableFieldFormatter;
use verbb\supertable\fields\SuperTableField;

class FieldFormatterFactory
{

    protected array $mappings = [
        EntriesField::class    => EntriesFieldFormatter::class,
        AssetsField::class     => AssetsFieldFormatter::class,
        MatrixField::class     => MatrixFieldFormatter::class,
        SuperTableField::class => SuperTableFieldFormatter::class,
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
