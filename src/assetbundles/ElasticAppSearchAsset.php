<?php

namespace GlueAgency\ElasticAppSearch\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ElasticAppSearchAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = '@elastic-app-search/resources/src/';

        $this->js = [
            'js/app-search.js',
        ];

        parent::init();
    }
}
