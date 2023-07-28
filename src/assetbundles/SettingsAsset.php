<?php

namespace GlueAgency\ElasticAppSearch\assetbundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class SettingsAsset extends AssetBundle
{

    public function init()
    {
        $this->sourcePath = '@elastic-app-search/resources/src/';

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/settings.js',
        ];

        parent::init();
    }
}
