<?php
/**
 * Eventsky plugin for Craft CMS 3.x
 *
 * Craft plugin for event management and attendee registration
 *
 * @link      https://fredmansky.at/p/impressum
 * @copyright Copyright (c) 2019 Fredmansky GmbH
 */

/**
 * Eventsky en Translation
 *
 * Returns an array with the string to be translated (as passed to `Craft::t('eventsky', '...')`) as
 * the key, and the translation as the value.
 *
 * http://www.yiiframework.com/doc-2.0/guide-tutorial-i18n.html
 *
 * @author    Fredmansky
 * @package   Eventsky
 * @since     0.0.1
 */
return [
    // ----------------------
    // ----- Templates ------
    // ----------------------
    'translate.events.title' => 'Events',
    'translate.events.cpTitle' => 'Events',

    'translate.tickets.title' => 'Tickets',
    'translate.tickets.cpTitle' => 'Tickets',

    'translate.ticketTypes.title' => 'Ticket Types',
    'translate.ticketTypes.cpTitle' => 'Ticket Types',

    'translate.eventTypes.title' => 'Event Types',
    'translate.eventTypes.cpTitle' => 'Event Types',
    'translate.eventTypes.new' => 'New Event Type',

    'translate.settings.title' => 'Settings',
    'translate.settings.cpTitle' => 'Settings',
    
    // -------------------
    // ----- Events ------
    // -------------------
    
    'translate.elements.Event.displayName' => 'Event',
    'translate.elements.Event.pluralDisplayName' => 'Events',
    'translate.elements.Event.sideBar.allEvents' => 'All Events',
    'translate.elements.Event.search.description' => 'Description',

    // ------------------------
    // ----- Event Types ------
    // ------------------------

    'translate.eventTypes.name' => 'Name',
    'translate.eventTypes.handle' => 'Handle',
    'translate.eventTypes.new' => 'Create a new event type',
    'translate.eventTypes.edit' => 'Edit event type',
    'translate.eventTypes.fieldLayout' => 'Field layout',
    'translate.eventType.fieldLayout.headline' => 'Field layout of event type “{name}”',
    'translate.eventTypes.fieldLayout.edit' => 'Edit field layout',
    'translate.eventTypes.delete' => 'Delete',
    'translate.eventType.tab.settings' => 'Settings',
    'translate.eventType.tab.fieldlayout' => 'Field Layout',
    'translate.eventTypes.deleteMessage' => 'Are you sure you want to delete “{ name }” and all its events?',

    // -------------------
    // ----- Tickets ------
    // -------------------
    'translate.elements.Ticket.displayName' => 'Ticket',
    'translate.elements.Ticket.pluralDisplayName' => 'Tickets',
    'translate.elements.Ticket.sideBar.allTickets' => 'All Events',
    'translate.elements.Ticket.search.description' => 'Description',

    // -------------------
    // ----- Ticket Types ------
    // -------------------
    'translate.elements.TicketType.displayName' => 'Ticket Type',
    'translate.elements.TicketType.pluralDisplayName' => 'Ticket Types',
    'translate.elements.TicketType.sideBar.allTicketTypes' => 'All Ticket Types',
    'translate.elements.TicketType.search.description' => 'Description',
];
