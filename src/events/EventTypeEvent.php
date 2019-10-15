<?php

namespace fredmansky\eventsky\events;

use fredmansky\eventsky\models\EventType;
use yii\base\Event;

class EventTypeEvent extends Event
{
    /** @var EventType|null */
    public $eventType;

    /** @var bool Whether the section is brand new */
    public $isNew = false;
}
