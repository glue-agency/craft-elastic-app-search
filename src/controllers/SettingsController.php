<?php

namespace GlueAgency\ElasticAppSearch\controllers;

use Craft;
use craft\web\Controller;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use yii\web\Response;

class SettingsController extends Controller
{

    public function actionFieldsTemplateForAuthType(): Response
    {
        $request = Craft::$app->getRequest();

        if(! $type = $request->get('type')) {

        }

        $view = Craft::$app->getView();
        $html = $view->renderTemplate("elastic-app-search/settings/auth/{$type}", [
            'settings' => ElasticAppSearch::getInstance()->getSettings(),
        ]);

        return $this->asJson([
            'settingsHtml' => $html,
            'headHtml' => $view->getHeadHtml(),
            'bodyHtml' => $view->getBodyHtml(),
        ]);
    }
}
