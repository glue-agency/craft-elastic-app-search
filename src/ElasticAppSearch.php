<?php

namespace GlueAgency\ElasticAppSearch;

use Craft;
use craft\base\Element;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\Json;
use craft\helpers\Queue;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;
use GlueAgency\ElasticAppSearch\assetbundles\ElasticAppSearchAsset;
use GlueAgency\ElasticAppSearch\assetbundles\SettingsAsset;
use GlueAgency\ElasticAppSearch\helpers\EngineHelper;
use GlueAgency\ElasticAppSearch\models\Settings;
use GlueAgency\ElasticAppSearch\queue\jobs\settings\CreateEnginesJob;
use GlueAgency\ElasticAppSearch\services\ClientService;
use GlueAgency\ElasticAppSearch\services\DocumentService;
use GlueAgency\ElasticAppSearch\services\EngineService;
use GlueAgency\ElasticAppSearch\services\IndexingService;
use GlueAgency\ElasticAppSearch\services\SearchService;
use GlueAgency\ElasticAppSearch\utilities\ElasticAppSearchUtility;
use GlueAgency\ElasticAppSearch\variables\ElasticAppSearchVariable;
use yii\base\Event;

/**
 * Elastic App Search plugin
 *
 * @method static ElasticAppSearch getInstance()
 *
 * @method Settings getSettings()
 * @property Settings $settings
 *
 * @property ClientService $client
 * @property DocumentService $documents
 * @property EngineService $engines
 * @property IndexingService $indexing
 * @property SearchService $search
 *
 * @author Glue Agency
 * @copyright Glue Agency
 * @license MIT
 */
class ElasticAppSearch extends Plugin
{
    public string $schemaVersion = '1.0.0';

    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                'client'    => ClientService::class,
                'documents' => DocumentService::class,
                'engines'   => EngineService::class,
                'indexing' => IndexingService::class,
                'search'    => SearchService::class,
            ],
        ];
    }

    public function init(): void
    {
        Craft::setAlias('@elastic-app-search', $this->getBasePath());

        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->registerControllers();

            $this->attachEventHandlers();

            if(ElasticAppSearch::getInstance()->getSettings()->exposeJsVars && Craft::$app->getRequest()->getIsSiteRequest()) {
                $variables = [
                    'elasticAppSearch' => [
                        'searchKey' => ElasticAppSearch::getInstance()->getSettings()->searchKey,
                        'endpointBase' => ElasticAppSearch::getInstance()->getSettings()->endpoint . ':' . ElasticAppSearch::getInstance()->getSettings()->port,
                        'engineName' => ElasticAppSearch::getInstance()->getSettings()->getIndexNameBySite(Craft::$app->sites->getCurrentSite()),
                    ]
                ];

                Craft::$app->getView()->registerJsWithVars(
                    fn($variables) => "Object.assign(window, $variables)",
                    [$variables],
                    View::POS_BEGIN
                );
            }
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    public function beforeSaveSettings(): bool
    {
        // Store only enabled entry handles
        $this->getSettings()->entryHandles = array_keys(array_filter($this->getSettings()->entryHandles));

        // Issue with form data that is posted when it should not be posted
        if($config = Craft::$app->getConfig()->getConfigFromFile('elastic-app-search')) {

            // Set the settings to the config values, otherwise
            // events in the same request receive the wrong data
            if(in_array('sites', array_keys($config))) {
                $this->getSettings()->sites = $config['sites'];
            }
        }

        return parent::beforeSaveSettings();
    }

    public function afterSaveSettings(): void
    {
        Queue::push(new CreateEnginesJob([
            'sites' => $this->getSettings()->sites
        ]));

        parent::afterSaveSettings();
    }

    protected function settingsHtml(): ?string
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(SettingsAsset::class);
        $view->registerJs('new Craft.ElasticAppSearch.Settings('.
            Json::encode('settings', JSON_UNESCAPED_UNICODE) .
        ');');

        return $view->renderTemplate('elastic-app-search/settings/index', [
            'settings'   => $this->getSettings(),
            'sites'      => Craft::$app->getSites()->getAllSites(),
            'entryTypes' => Craft::$app->getSections()->getAllEntryTypes(),
        ]);
    }

    protected function registerControllers(): void
    {
        if(Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'GlueAgency\\ElasticAppSearch\\console\\controllers';

            return;
        }

        $this->controllerNamespace = 'GlueAgency\\ElasticAppSearch\\controllers';
    }

    protected function attachEventHandlers(): void
    {
        Event::on(View::class, View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $event) {
            $event->roots['elastic-app-search'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates';
        });

        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES, function (RegisterComponentTypesEvent $event) {
            $event->types[] = ElasticAppSearchUtility::class;
        });

        Event::on(Entry::class, Element::EVENT_AFTER_SAVE, function(ModelEvent $event) {
            ElasticAppSearch::getInstance()->indexing->index($event->sender);
        });

        Event::on(Entry::class, Element::EVENT_AFTER_DELETE, function(Event $event) {
            ElasticAppSearch::getInstance()->indexing->delete($event->sender);
        });

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            $event->sender->set('elasticAppSearch', ElasticAppSearchVariable::class);
        });
    }
}
