<?php

namespace GlueAgency\ElasticAppSearch\controllers;

use Craft;
use craft\helpers\Queue;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\queue\jobs\settings\CreateEnginesJob;
use GlueAgency\ElasticAppSearch\queue\jobs\utility\IndexEntryTypesJob;
use GlueAgency\ElasticAppSearch\queue\jobs\utility\RemoveAllEntriesJob;
use yii\web\Response;

class UtilitiesController extends Controller
{

    public function actionReindex(): Response
    {
        $this->requirePostRequest();
        $selected = Craft::$app->getRequest()->getBodyParam('entryHandles');

        if($selected === '*') {
            $toIndex = ElasticAppSearch::getInstance()->settings->entryHandles;
        }

        if(is_array($selected)) {
            $toIndex = array_filter($selected, function($item) {
                return in_array($item, ElasticAppSearch::getInstance()->settings->entryHandles);
            });
        }

        if(! isset($toIndex) || empty($toIndex)) {
            Craft::$app->getSession()->setError(Craft::t('elastic-app-search', 'No Entry Types selected.'));
        } else {
            foreach(ElasticAppSearch::getInstance()->settings->sites as $handle => $indexName) {
                $site = Craft::$app->sites->getSiteByHandle($handle);

                Queue::push(new IndexEntryTypesJob([
                    'entryHandles' => $toIndex,
                    'siteId'       => $site->id,
                ]));
            }

            Craft::$app->getSession()->setSuccess(Craft::t('elastic-app-search', 'Indexing started.'));
        }

        return $this->redirect(UrlHelper::cpUrl('utilities/elastic-app-search'));
    }

    public function actionClear(): Response
    {
        foreach(ElasticAppSearch::getInstance()->getSettings()->sites as $handle => $indexName) {
            $site = Craft::$app->sites->getSiteByHandle($handle);

            Queue::push(new RemoveAllEntriesJob([
                'siteId' => $site->id,
            ]));
        }

        Craft::$app->getSession()->setSuccess(Craft::t('elastic-app-search', 'Removal started.'));

        return $this->redirect(UrlHelper::cpUrl('utilities/elastic-app-search'));
    }

    public function actionCreateEngines(): Response
    {
        Queue::push(new CreateEnginesJob([
            'sites' => ElasticAppSearch::getInstance()->getSettings()->sites,
        ]));

        Craft::$app->getSession()->setSuccess(Craft::t('elastic-app-search', 'Creating engines.'));

        return $this->redirect(UrlHelper::cpUrl('utilities/elastic-app-search'));
    }
}
