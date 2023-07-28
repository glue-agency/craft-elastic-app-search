<?php

namespace GlueAgency\ElasticAppSearch\presenters\search\fields;

use DateTime;
use GlueAgency\ElasticAppSearch\presenters\BasePresenter;
use Stringable;

class DateFieldPresenter extends BasePresenter implements Stringable
{

    public string $raw;

    public function __toString(): string
    {
        return new DateTime($this->raw);
    }
}
