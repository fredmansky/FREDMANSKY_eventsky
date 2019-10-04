<?php

return [
    'eventsky/eventtypes' => 'eventsky/event-types/index',
    'eventsky/eventtype/new' => 'eventsky/event-types/edit',
    'eventsky/eventtype/<eventTypeId:\d+>/fieldlayout' => 'eventsky/event-types/field-layout',
    'eventsky/eventtype/<eventTypeId:\d+>' => 'eventsky/event-types/edit',
];