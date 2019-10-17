<?php

return [
    'eventsky/eventtypes' => 'eventsky/event-types/index',
    'eventsky/eventtype/new' => 'eventsky/event-types/edit',
    'eventsky/eventtype/<eventTypeId:\d+>' => 'eventsky/event-types/edit',
    'eventsky/tickettypes' => 'eventsky/ticket-types/index',
    'eventsky/tickettype/new' => 'eventsky/ticket-types/edit',
    'eventsky/tickettype/delete/<ticketTypeId:\d+>' => 'eventsky/ticket-types/delete',
    'eventsky/tickettype/<ticketTypeId:\d+>' => 'eventsky/ticket-types/edit',
];