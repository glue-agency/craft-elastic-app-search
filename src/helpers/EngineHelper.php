<?php

namespace GlueAgency\ElasticAppSearch\helpers;

use Elastic\EnterpriseSearch\AppSearch\Schema\Engine;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\presenters\EnginePresenter;

class EngineHelper
{

    public static function asEngine(string $engine): Engine
    {
        $site = ElasticAppSearch::getInstance()->settings->getSiteByIndexName($engine);

        $engine = new Engine($engine);
        $engine->language = $site->language;

        return $engine;
    }

    public static function asString(string|EnginePresenter $engine): string
    {
        if($engine instanceof EnginePresenter) {
            return $engine->name;
        }

        return $engine;
    }
}
