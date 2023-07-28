<?php

namespace GlueAgency\ElasticAppSearch\formatters;

use craft\elements\Entry;

interface ElementFormatterInterface
{

    public static function format(Entry $entry): mixed;
}
