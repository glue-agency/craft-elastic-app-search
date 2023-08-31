<?php

namespace GlueAgency\ElasticAppSearch\mappers\schemas;

use Craft;
use craft\base\FieldInterface;
use craft\fields\Date;
use craft\fields\Number;
use craft\helpers\ArrayHelper;
use Elastic\EnterpriseSearch\AppSearch\Schema\SchemaUpdateRequest;
use GlueAgency\ElasticAppSearch\helpers\FieldHelper;

abstract class BaseSchema
{

    const TEXT = 'text';
    const DATE = 'date';
    const NUMBER = 'number';
    const GEO = 'geolocation';

    public function build(): SchemaUpdateRequest
    {
        $schema = new SchemaUpdateRequest;

        $this->schema($schema);
        $this->searchableFields($schema);

        return $schema;
    }

    protected function searchableFields(SchemaUpdateRequest $schema): SchemaUpdateRequest
    {
        $searchableCustomFields = ArrayHelper::where(Craft::$app->getFields()->getAllFields(), function(FieldInterface $field) {
            return $field->searchable;
        }, true, true, false);

        foreach($searchableCustomFields as $field) {
            $handle = FieldHelper::toElasticSafeName($field);
            $type = self::TEXT;

            if($field instanceof Date) {
                $type = self::DATE;

                // Add a helper field for when the date is emtpy
                $helperHandle = "no_{$handle}";
                $schema->{$helperHandle} = self::TEXT;
            }

            if($field instanceof Number) {
                $type = self::NUMBER;
            }

            $schema->{$handle} = $type;
        }

        return $schema;
    }
}
