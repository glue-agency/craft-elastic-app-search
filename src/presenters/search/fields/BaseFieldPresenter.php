<?php

namespace GlueAgency\ElasticAppSearch\presenters\search\fields;

use GlueAgency\ElasticAppSearch\presenters\BasePresenter;
use Stringable;

class BaseFieldPresenter extends BasePresenter implements Stringable
{

    public mixed $raw;

    public string|null $snippet = null;

    public function __toString(): string
    {
        if(is_array($this->raw)) {
            return implode(', ', $this->raw);
        }

        return $this->raw;
    }
}
