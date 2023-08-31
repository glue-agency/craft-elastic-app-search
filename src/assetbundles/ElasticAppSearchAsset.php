<?php

namespace GlueAgency\ElasticAppSearch\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ElasticAppSearchAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = '@elastic-app-search/resources/src/';

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/elastic-app-search.css',
        ];

        parent::init();
    }
}
