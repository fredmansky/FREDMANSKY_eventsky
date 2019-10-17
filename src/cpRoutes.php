<?php

return [
    'eventsky/eventtypes' => 'eventsky/event-types/index',
    'eventsky/eventtype/new' => 'eventsky/event-types/edit',
    'eventsky/eventtype/<eventTypeId:\d+>' => 'eventsky/event-types/edit',

    'eventsky/tickets' => 'eventsky/tickets/index',
    'eventsky/ticket/new' => 'eventsky/tickets/edit',
    'eventsky/ticket/delete/<ticketId:\d+><slug:(?:-[^\/]*)?>' => 'eventsky/tickets/delete',
    'eventsky/ticket/<ticketId:\d+><slug:(?:-[^\/]*)?>' => 'eventsky/tickets/edit',

    'eventsky/tickettypes' => 'eventsky/ticket-types/index',
    'eventsky/tickettype/new' => 'eventsky/ticket-types/edit',
    'eventsky/tickettype/<ticketTypeId:\d+>' => 'eventsky/ticket-types/edit',
];