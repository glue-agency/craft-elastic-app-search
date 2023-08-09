<?php

namespace GlueAgency\ElasticAppSearch\formatters\fields;

use craft\base\Element;
use craft\base\Field;

class DateFieldFormatter implements FieldFormatterInterface
{

    public function format(Element $element, Field $field): mixed
    {
        $value = $element->getFieldValue($field->handle);

        if($value) {
            if($field->showDate && $field->showTime){
                return $value->format('c');
            }

            if($field->showDate) {
                return $value->format('Y-m-d');
            }

            if($field->showTime) {
                return $value->format('H:i:s');
            }
        }

        return null;
    }
}
