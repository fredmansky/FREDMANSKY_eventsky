<?php

namespace fredmansky\eventsky\events;

use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\models\TicketType;
use yii\base\Event;

class TicketSaveEvent extends Event
{
    public $ticket;

    public $eventId;

    public $isNew = false;
}
