<?php

namespace GlueAgency\ElasticAppSearch\services;

use craft\base\Element;
use craft\elements\Entry;
use craft\helpers\Queue;
use GlueAgency\ElasticAppSearch\ElasticAppSearch;
use GlueAgency\ElasticAppSearch\queue\jobs\entry\DeleteEntryJob;
use GlueAgency\ElasticAppSearch\queue\jobs\entry\IndexEntryJob;
use yii\base\Component;

class IndexingService extends Component
{

    public function index(Element $element): void
    {
        // @todo check if indexing is turned off

        if(ElasticAppSearch::getInstance()->settings->shouldBeIndexed($element)) {
            if($element instanceof Entry) {
                Queue::push(new IndexEntryJob([
                    'entryId' => $element->id,
                    'siteId'  => $element->siteId,
                ]));
            }
        }
    }

    public function delete(Element $element): void
    {
        // @todo check if indexing is turned off

        if(ElasticAppSearch::getInstance()->settings->shouldBeIndexed($element)) {

            if($element instanceof Entry) {
                Queue::push(new DeleteEntryJob([
                    'entryId'    => $element->id,
                    'siteId'  => $element->siteId,
                ]));
            }
        }
    }
}
