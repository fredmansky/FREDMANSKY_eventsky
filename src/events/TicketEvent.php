<?php

namespace fredmansky\eventsky\events;

use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\models\TicketType;
use yii\base\Event;

class TicketEvent extends Event
{
    /** @var Ticket|null */
    public $ticket;

    /** @var TicketType|null */
    public $ticketType;

    /** @var bool Whether the section is brand new */
    public $isNew = false;

    /** @var bool Whether the ticket type is changed for the ticket */
    public $switchType = false;
}
