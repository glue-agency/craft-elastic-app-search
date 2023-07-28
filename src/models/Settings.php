<?php

namespace GlueAgency\ElasticAppSearch\models;

use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;
use craft\elements\Entry;
use craft\helpers\App;
use craft\helpers\ElementHelper;
use craft\models\Site;
use insolita\ArrayStructureValidator\ArrayStructureValidator;

class Settings extends Model
{

    const AUTH_BASIC = 'basic';
    const AUTH_TOKEN = 'token';
    const AUTH_API_KEY = 'apiKey';

    public string $endpoint = '';

    public string $port = '9200';

    public string $auth = 'basic';

    public string $username = 'elastic';

    public string $password = '';

    public string $token = '';

    public string $apiKey = '';

    public string $searchKey = '';

    public array $sites = [];

    public array $entryHandles = [];

    public bool $exposeJsVars = true;

    public function defineBehaviors(): array
    {
        return [
            'parser' => [
                'class' => EnvAttributeParserBehavior::class,
                'attributes' => [
                    'endpoint',
                    'port',
                    'auth',
                    'username',
                    'password',
                    'token',
                    'apiKey',
                    'searchKey',
                    'exposeJsVars',
                ],
            ]
        ];
    }

    public function shouldBeIndexed(Entry $entry): bool
    {
        if(ElementHelper::isDraftOrRevision($entry)) {
            return false;
        }

        if(! in_array($entry->type->handle, $this->entryHandles)) {
            return false;
        }

        return true;
    }

    public function isSiteConfigured(Site $site): bool
    {
        return in_array($site->handle, array_map(function($site) {
            return ! empty($site['index']);
        }, array_filter($this->sites, function($site) {
            return isset($site['enabled']) && $site['enabled'];
        })));
    }

    public function getIndexNameBySite(Site $site): ?string
    {
        return $this->sites[$site->handle]['index'] ?? null;
    }

    public function getIndexNameBySiteHandle(string $handle): ?string
    {
        return $this->sites[$handle]['index'] ?? null;
    }

    public function defineRules(): array
    {
        $rules = [];

        $rules[] = [['endpoint', 'port', 'auth', 'searchKey'], 'required'];
        $rules[] = ['sites', ArrayStructureValidator::class,
            'each' => true,
            'rules' => [
                'enabled' => [
                    ['boolean']
                ],
                'index' => [
                    ['required', 'when' => function($model) {
                        return !! $model->enabled;
                    }]
                ],
            ]
        ];

        return $rules;
    }
}
