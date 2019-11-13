<?php

namespace fredmansky\eventsky\db;

abstract class Table extends \craft\db\Table
{
    const EVENTS = '{{%eventsky_events}}';
    const EVENT_TICKET_TYPES = '{{%eventsky_events_tickettypes}}';
    const EVENT_TYPES = '{{%eventsky_eventtypes}}';
    const EVENT_TYPES_SITES = '{{%eventsky_eventtypes_sites}}';
    const TICKETS = '{{%eventsky_tickets}}';
    const TICKET_STATUSES = '{{%eventsky_ticketstatuses}}';
    const TICKET_TYPES = '{{%eventsky_tickettypes}}';
}