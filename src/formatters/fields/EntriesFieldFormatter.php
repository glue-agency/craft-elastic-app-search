<?php

namespace GlueAgency\ElasticAppSearch\formatters\fields;

use craft\base\Element;
use craft\base\Field;

class EntriesFieldFormatter implements FieldFormatterInterface
{

    public function format(Element $element, Field $field): mixed
    {
        $related = $element->getFieldValue($field->handle)->all();

        return array_column($related, 'title');
    }
}
