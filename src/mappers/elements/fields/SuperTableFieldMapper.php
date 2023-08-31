<?php

namespace GlueAgency\ElasticAppSearch\mappers\elements\fields;

use craft\base\Element;
use craft\base\Field;
use GlueAgency\ElasticAppSearch\factories\FieldMappingFactory;

class SuperTableFieldMapper implements FieldMapperInterface
{

    public function format(Element $element, Field $field): mixed
    {
        $data = [];

        $fieldFormatterFactory = new FieldMappingFactory;
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
