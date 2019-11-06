<?php

namespace fredmansky\eventsky\events;

use fredmansky\eventsky\models\TicketType;
use yii\base\Event;

class TicketTypeEvent extends Event
{
    /** @var TicketType|null */
    public $ticketType;

    /** @var bool Whether the section is brand new */
    public $isNew = false;
}
