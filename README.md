# Elastic App Search

Index Craft Entries to Elastic App Search

## Requirements

This plugin requires Craft CMS 4.0.0 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Elastic App Search”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require glue-agency/craft-elastic-app-search

# tell Craft to install the plugin
./craft plugin/install elastic-app-search
```

## Usage

### Backend

WIP

### Frontend

Install the elasticsearch `@elastic/app-search-javascript` package.

The ElasticAppSearch Plugin config `elastic-app-search.php` has a setting `exposeJsVars`. When this is set to `true`,
you are able to access the `searchKey` `endpointBase` and `engineName` that are needed to setup the Elastic Client.

Setup the Client

```js 
ElasticAppSearch.createClient({
    searchKey: window.elasticAppSearch.searchKey,
    endpointBase: window.elasticAppSearch.endpointBase,
    engineName: window.elasticAppSearch.engineName
});
```

## Configuration

If you create a `elastic-app-search.php` file in the `config` folder you can overwrite the following plugin settings. The config file supports [multi env setup](https://craftcms.com/docs/4.x/config/#multi-environment-configs).

```php
<?php

return [
    /**
     * Connection settings
     */
    'endpoint'  => 'https://your-endpoint.found.io',
    'port'      => '9200',

    /** 
     * Authentication
     * 
     * Supported auth keys: "basic", "apiKey", "token"
     * 
     * Each auth key requires different settings:
     * basic: "username", "password"
     * apiKey: "apiKey"
     * token: "token"
     */
    'auth'      => 'apiKey',
    'apiKey'    => 'private-xxxxxxxxxxxxxxxxxxxx',

    /** 
     * Search
     */
    'searchKey' => 'search-xxxxxxxxxxxxxxxxxxxx',

    /** 
     * Sites
     * 
     * Manage Site indexing and linked indexes. Always requires
     * the "enabled" and "index" key for each indexable site. 
     * Missing sites will not be indexed.
     */
    'sites'     => [
        'site_nl' => [
            'enabled' => true,
            'index'   => 'site_index_nl',
        ],
        'site_en' => [
            'enabled' => true,
            'index'   => 'site_index_en',
        ],
    ],
];
```
