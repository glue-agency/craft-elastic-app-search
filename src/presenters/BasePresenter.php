<?php

namespace GlueAgency\ElasticAppSearch\presenters;

use craft\helpers\StringHelper;

abstract class BasePresenter implements Presenter
{

    public function __construct($attributes)
    {
        $this->initializeAttributes($attributes);
    }

    protected function initializeAttributes(array $attributes): void
    {
        foreach($attributes as $key => $value) {
            // Allow Presenters to modify incoming attributes
            // when using a "configure{CamelizedKey}" function
            if(method_exists($this, $method = 'configure' . StringHelper::upperCamelize($key))) {
                $this->{$method}($value);

                continue;
            }

            $this->{$key} = $value;
        }
    }
}
