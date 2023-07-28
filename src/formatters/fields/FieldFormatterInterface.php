<?php

namespace GlueAgency\ElasticAppSearch\formatters\fields;

use craft\base\Element;
use craft\base\Field;

interface FieldFormatterInterface
{

    public function format(Element $element, Field $field): mixed;
}
