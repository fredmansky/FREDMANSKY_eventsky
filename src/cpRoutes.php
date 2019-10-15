<?php

return [
    'eventsky/events' => 'eventsky/events/index',
    'eventsky/event/new' => 'eventsky/events/edit',
    'eventsky/event/<entryId:\d+><slug:(?:-[^\/]*)?>' => 'eventsky/events/edit',

    'eventsky/eventtypes' => 'eventsky/event-types/index',
    'eventsky/eventtype/new' => 'eventsky/event-types/edit',
    'eventsky/eventtype/<eventTypeId:\d+>' => 'eventsky/event-types/edit',
];