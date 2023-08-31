<?php

namespace GlueAgency\ElasticAppSearch\mappers\elements;

use craft\elements\Entry;

interface ElementMapperInterface
{

    public static function format(Entry $entry): mixed;
}
