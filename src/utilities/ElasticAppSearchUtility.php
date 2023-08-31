<?php

namespace GlueAgency\ElasticAppSearch\utilities;

use Craft;
use craft\base\Utility;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;

class ElasticAppSearchUtility extends Utility
{

    public static function displayName(): string
    {
        return Craft::t('elastic-app-search', 'Elastic App Search');
    }

    public static function id(): string
    {
        return 'elastic-app-search';
    }

    public static function iconPath(): ?string
    {
        return ElasticAppSearch::getInstance()->getBasePath() . DIRECTORY_SEPARATOR . 'icon-mask.svg';
    }

    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('elastic-app-search/utility/index', [
            'sites'        => ElasticAppSearch::getInstance()->settings->sites,
            'entryTypes'   => Craft::$app->getSections()->getAllEntryTypes(),
            'entryHandles' => ElasticAppSearch::getInstance()->settings->entryHandles,
        ]);
    }
}
