<?php

namespace GlueAgency\ElasticAppSearch\helpers;

use GlueAgency\ElasticAppSearch\presenters\EnginePresenter;

class EngineHelper
{

    public static function asString(string|EnginePresenter $engine): string
    {
        if($engine instanceof EnginePresenter) {
            return $engine->name;
        }

        return $engine;
    }
}
