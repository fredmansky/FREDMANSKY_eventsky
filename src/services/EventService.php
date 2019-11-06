<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\records\EventRecord;
use yii\db\ActiveQuery;

class EventService extends Component
{
    /** @var array */
    private $events;

    public function init()
    {
        parent::init();
    }

    public function getEventById(int $id): ?ElementInterface
    {
        if (!$id) {
            return null;
        }

        return Craft::$app->getElements()->getElementById($id, Event::class);
    }
}
