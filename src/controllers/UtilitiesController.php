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
use GlueAgency\ElasticAppSearch\queue\jobs\utility\UpdateSchemaJob;
use yii\web\Response;

class UtilitiesController extends Controller
{

    public function actionIndex(): Response
    {
        $this->requirePostRequest();
        $index = Craft::$app->getRequest()->getBodyParam('index');
        $siteHandle = Craft::$app->getRequest()->getBodyParam('siteHandle');

        $site = Craft::$app->sites->getSiteByHandle($siteHandle);
        Queue::push(new IndexEntryTypesJob([
            'entryHandles' => ElasticAppSearch::getInstance()->settings->entryHandles,
            'siteId' => $site->id,
        ]));

        Craft::$app->getSession()->setSuccess(Craft::t('elastic-app-search', "Reindexing '{$index}'."));

        return $this->redirect(UrlHelper::cpUrl('utilities/elastic-app-search'));
    }

    public function actionRefresh(): Response
    {
        $this->requirePostRequest();
        $index = Craft::$app->getRequest()->getBodyParam('index');
        $siteHandle = Craft::$app->getRequest()->getBodyParam('siteHandle');

        $site = Craft::$app->sites->getSiteByHandle($siteHandle);
        Queue::push(new RemoveAllEntriesJob([
            'siteId' => $site->id,
        ]));
        Queue::push(new IndexEntryTypesJob([
            'entryHandles' => ElasticAppSearch::getInstance()->settings->entryHandles,
            'siteId' => $site->id,
        ]));

        Craft::$app->getSession()->setSuccess(Craft::t('elastic-app-search', "Refreshing index {$index}."));

        return $this->redirect(UrlHelper::cpUrl('utilities/elastic-app-search'));
    }

    public function actionSchema(): Response
    {
        $this->requirePostRequest();
        $index = Craft::$app->getRequest()->getBodyParam('index');
        $siteHandle = Craft::$app->getRequest()->getBodyParam('siteHandle');

        $site = Craft::$app->sites->getSiteByHandle($siteHandle);
        Queue::push(new UpdateSchemaJob([
            'siteId' => $site->id,
        ]));

        Craft::$app->getSession()->setSuccess(Craft::t('elastic-app-search', "Updating Schema index {$index}."));

        return $this->redirect(UrlHelper::cpUrl('utilities/elastic-app-search'));
    }

    public function actionFlush(): Response
    {
        $this->requirePostRequest();
        $index = Craft::$app->getRequest()->getBodyParam('index');
        $siteHandle = Craft::$app->getRequest()->getBodyParam('siteHandle');

        $site = Craft::$app->sites->getSiteByHandle($siteHandle);
        Queue::push(new RemoveAllEntriesJob([
            'siteId' => $site->id,
        ]));

        Craft::$app->getSession()->setSuccess(Craft::t('elastic-app-search', "Flushing index {$index}."));

        return $this->redirect(UrlHelper::cpUrl('utilities/elastic-app-search'));
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $index = Craft::$app->getRequest()->getBodyParam('index');
        $siteHandle = Craft::$app->getRequest()->getBodyParam('siteHandle');

        ElasticAppSearch::getInstance()->engines->delete($index);

        Craft::$app->getSession()->setSuccess(Craft::t('elastic-app-search', "Deleting index '{$index}'."));

        return $this->redirect(UrlHelper::cpUrl('utilities/elastic-app-search'));
    }

    public function actionCreate(): Response
    {
        $this->requirePostRequest();
        $index = Craft::$app->getRequest()->getBodyParam('index');
        $siteHandle = Craft::$app->getRequest()->getBodyParam('siteHandle');

        ElasticAppSearch::getInstance()->engines->create($index);

        Craft::$app->getSession()->setSuccess(Craft::t('elastic-app-search', "Creating index '{$index}'."));

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
