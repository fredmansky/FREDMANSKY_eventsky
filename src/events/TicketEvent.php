<?php

namespace fredmansky\eventsky\events;

use fredmansky\eventsky\elements\Ticket;
use yii\base\Event;

class TicketEvent extends Event
{
    /** @var Ticket|null */
    public $ticket;

    /** @var bool Whether the section is brand new */
    public $isNew = false;
}
