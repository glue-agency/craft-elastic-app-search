<?php

namespace GlueAgency\ElasticAppSearch\formatters\fields;

use craft\base\Element;
use craft\base\Field;
use GlueAgency\ElasticAppSearch\factories\FieldFormatterFactory;

class SuperTableFieldFormatter implements FieldFormatterInterface
{

    public function format(Element $element, Field $field): mixed
    {
        $data = [];

        $fieldFormatterFactory = new FieldFormatterFactory;
        foreach($element->getFieldValue($field->handle)->all() as $block) {
            foreach($block->getFieldLayout()->getCustomFields() as $field) {
                if($field->searchable) {
                    $data[] = $fieldFormatterFactory->format($block, $field);
                }
            }
        }

        return array_filter($data);
    }
}
