<?php

return [
    'eventsky' => 'eventsky/events/index',
    'eventsky/events' => 'eventsky/events/index',
    'eventsky/event/new' => 'eventsky/events/edit',
    'eventsky/event/<eventId:\d+><slug:(?:-[^\/]*)?>/tickets' => 'eventsky/events/tickets',
    'eventsky/event/<eventId:\d+><slug:(?:-[^\/]*)?>' => 'eventsky/events/edit',

    'eventsky/eventtypes' => 'eventsky/event-types/index',
    'eventsky/eventtype/new' => 'eventsky/event-types/edit',
    'eventsky/eventtype/<eventTypeId:\d+>' => 'eventsky/event-types/edit',

    'eventsky/tickets' => 'eventsky/tickets/index',
    'eventsky/ticket/new' => 'eventsky/tickets/edit',
    'eventsky/ticket/delete/<ticketId:\d+><slug:(?:-[^\/]*)?>' => 'eventsky/tickets/delete',
    'eventsky/ticket/<ticketId:\d+><slug:(?:-[^\/]*)?>' => 'eventsky/tickets/edit',

    'eventsky/tickettypes' => 'eventsky/ticket-types/index',
    'eventsky/tickettype/new' => 'eventsky/ticket-types/edit',
    'eventsky/tickettype/delete/<ticketTypeId:\d+>' => 'eventsky/ticket-types/delete',
    'eventsky/tickettype/<ticketTypeId:\d+>' => 'eventsky/ticket-types/edit',

    'eventsky/emailnotifications' => 'eventsky/email-notifications/index',
    'eventsky/emailnotifications/new' => 'eventsky/email-notifications/edit',
    'eventsky/emailnotifications/<emailNotificationId:\d+>' => 'eventsky/email-notifications/edit',
    'eventsky/emailnotifications/delete/<emailNotificationId:\d+>' => 'eventsky/email-notifications/delete',
];
