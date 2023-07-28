<?php

namespace GlueAgency\ElasticAppSearch\presenters\search\fields;

use GlueAgency\ElasticAppSearch\presenters\BasePresenter;
use Stringable;

class MetaFieldPresenter extends BasePresenter implements Stringable
{

    public string $id;

    public string $engine;

    public float $score;

    public function __toString(): string
    {
        return $this->engine;
    }
}
