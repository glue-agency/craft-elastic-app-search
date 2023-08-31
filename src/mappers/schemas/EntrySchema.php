<?php

namespace GlueAgency\ElasticAppSearch\mappers\schemas;

use Elastic\EnterpriseSearch\AppSearch\Schema\SchemaUpdateRequest;

class EntrySchema extends BaseSchema implements SchemaMapperInterface
{

    public function schema(SchemaUpdateRequest $schema): SchemaUpdateRequest
    {
        $schema->title = self::TEXT;
        $schema->slug = self::TEXT;
        $schema->url = self::TEXT;
        $schema->enabled = self::TEXT;
        $schema->site_id = self::TEXT;
        $schema->site_language = self::TEXT;
        $schema->section_id = self::TEXT;
        $schema->section_handle = self::TEXT;
        $schema->type_id = self::TEXT;
        $schema->type_handle = self::TEXT;
        $schema->post_date = self::DATE;
        $schema->no_post_date = self::TEXT;
        $schema->expiry_date = self::DATE;
        $schema->no_expiry_date = self::TEXT;
        $schema->date_updated = self::DATE;
        $schema->date_created = self::DATE;

        return $schema;
    }
}
