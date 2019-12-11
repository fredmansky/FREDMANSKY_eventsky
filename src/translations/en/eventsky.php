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

    'translate.settings.title' => 'Settings',
    'translate.settings.cpTitle' => 'Settings',

    'translate.emailNotifications.title' => 'Email Notifications',
    'translate.emailNotifications.cpTitle' => 'Email Notifications',

    // -------------------
    // ----- Events ------
    // -------------------
    
    'translate.event.new' => 'Create a new event',
    'translate.event.saved' => 'Event saved.',
    'translate.event.notSaved' => 'Couldn’t save event.',
    'translate.event.edit' => 'Couldn’t save event.',
    'translate.event.notFound' => 'Event not found.',
    'translate.events.fields.eventStart' => 'Event Start',
    'translate.events.fields.eventEnd' => 'Event End',
    'translate.events.fields.details.eventtype' => 'Event Type',
    'translate.events.fields.details.slug' => 'Slug',
    'translate.events.fields.details.placeholder.slug' => 'Enter slug',
    'translate.events.fields.details.postDate' => 'Post Date',
    'translate.events.fields.details.expiryDate' => 'Expiry Date',
    'translate.events.fields.tickets.needsRegistration' => 'Registration needed',
    'translate.events.fields.tickets.totalTickets' => 'Total Number of Tickets',
    'translate.events.fields.tickets.hasWaitingList' => 'Waiting List',
    'translate.events.fields.tickets.waitingListSize' => 'Waiting List Size',
    'translate.events.fields.ticketType.registrationStart' => 'Registration Start Date',
    'translate.events.fields.ticketType.registrationEnd' => 'Registration End Date',
    'translate.events.fields.ticketType.limit' => 'Ticket Limit',
    'translate.events.tab.eventData' => 'Event Data',
    'translate.events.tab.tickets' => 'Tickets',
    'translate.elements.Event.displayName' => 'Event',
    'translate.elements.Event.pluralDisplayName' => 'Events',
    'translate.elements.Event.sideBar.allEvents' => 'All Events',
    'translate.elements.Event.sideBar.eventTypeHeading' => 'Event Types',
    'translate.events.fields.details.emailNotificationIdAdmin' => 'Email Notification for Admin',
    'translate.events.fields.details.adminEmail' => 'Admin Notification Email(s)',
    'translate.events.defaultEmailNotifications' => 'Default to Event Type Setting',

    // ------------------------
    // ----- Event Types ------
    // ------------------------

    'translate.eventTypes.name' => 'Name',
    'translate.eventTypes.handle' => 'Handle',
    'translate.eventTypes.new' => 'Create a new event type',
    'translate.eventTypes.edit' => 'Edit event type',
    'translate.eventTypes.save.success' => 'Event type saved.',
    'translate.eventTypes.save.error' => 'Couldn’t save event type.',
    'translate.eventTypes.notFound' => 'Event Type not found.',
    'translate.eventTypes.fieldLayout' => 'Field layout',
    'translate.eventType.fieldLayout.headline' => 'Field layout of event type “{name}”',
    'translate.eventTypes.fieldLayout.edit' => 'Edit field layout',
    'translate.eventTypes.delete' => 'Delete',
    'translate.eventType.tab.settings' => 'Settings',
    'translate.eventType.tab.fieldlayout' => 'Field Layout',
    'translate.eventTypes.deleteMessage' => 'Are you sure you want to delete “{name}“ and all its events?',
    'translate.eventTypes.fields.details.emailNotificationIdAdmin' => 'Email Notification for Admin',
    'translate.eventTypes.fields.details.adminEmail' => 'Admin Notification Email(s)',
    'translate.eventTypes.noEmailNotifications' => 'No Email Notifications',

    // --------------------
    // ----- Tickets ------
    // --------------------

    'translate.tickets.displayName' => 'Ticket',
    'translate.tickets.pluralDisplayName' => 'Tickets',
    'translate.tickets.sideBar.allTickets' => 'All Tickets',
    'translate.tickets.sideBar.ticketTypeHeading' => 'Ticket Types',
    'translate.tickets.search.ticketType' => 'Ticket Type',
    'translate.tickets.name' => 'Name',
    'translate.tickets.handle' => 'Handle',
    'translate.tickets.new' => 'Create a new Ticket',
    'translate.tickets.edit' => 'Edit Ticket',
    'translate.tickets.notFound' => 'Ticket not found.',
    'translate.tickets.fieldLayout' => 'Field layout',
    'translate.tickets.fieldLayout.edit' => 'Edit field layout',
    'translate.tickets.delete' => 'Delete',
    'translate.tickets.deleteMessage' => 'Are you sure you want to delete ticket “{ name }”?',
    'translate.tickets.ticketType' => 'Ticket Type ID',
    'translate.tickets.table.name' => 'Title',
    'translate.tickets.table.handle' => 'Handle',
    'translate.tickets.table.eventId' => 'Event ID',
    'translate.tickets.table.typeId' => 'Ticket Type ID',

    'translate.ticket.tab.settings' => 'Settings',
    'translate.ticket.tab.fieldlayout' => 'Field Layout',
    'translate.ticket.tab.ticketData' => 'Ticket Data',
    'translate.ticket.tab.event' => 'Ticket Event',
    'translate.ticket.fieldLayout.headline' => 'Field layout of ticket type “{name}”',
    'translate.ticket.new' => 'Create a new ticket',
    'translate.ticket.saved' => 'Ticket saved.',
    'translate.ticket.notSaved' => 'Couldn’t save ticket.',
    'translate.ticket.edit' => 'Couldn’t save ticket.',
    'translate.ticket.notFound' => 'Ticket not found.',
    'translate.ticket.fields.details.slug' => 'Slug',
    'translate.ticket.fields.details.tickettype' => 'Ticket Type',
    'translate.ticket.fields.details.event' => 'Event',
    'translate.ticket.fields.details.status' => 'Status',

    // ------------------------
    // ----- Ticket Types -----
    // ------------------------

    'translate.ticketTypes.name' => 'Name',
    'translate.ticketTypes.handle' => 'Handle',
    'translate.ticketTypes.new' => 'Create a new ticket type',
    'translate.ticketTypes.edit' => 'Edit ticket type',
    'translate.ticketTypes.save.error' => 'Couldn’t save ticket type.',
    'translate.ticketTypes.save.success' => 'Ticket type saved.',
    'translate.ticketTypes.notFound' => 'Ticket Type not found.',
    'translate.ticketTypes.fieldLayout' => 'Field layout',
    'translate.ticketTypes.fieldLayout.edit' => 'Edit field layout',
    'translate.ticketTypes.delete' => 'Delete',
    'translate.ticketTypes.deleteMessage' => 'Are you sure you want to delete ticket type “{name}” and all its tickets?',
    'translate.ticketType.tab.settings' => 'Settings',
    'translate.ticketType.tab.fieldlayout' => 'Field Layout',
    'translate.ticketType.fieldLayout.headline' => 'Field layout of event type “{name}”',
    'translate.ticketType.fields.details.emailNotificationIdUser' => 'Email Notification for User',
    'translate.ticketTypes.noEmailNotifications' => 'No Email Notifications',
    'translate.fieldlayout.notFound' => 'Field layout not found.',

    // -------------------------------
    // ----- Email Notifications -----
    // -------------------------------

    'translate.emailNotifications.new' => 'Create a new email notification',
    'translate.emailNotifications.name' => 'Name',
    'translate.emailNotifications.handle' => 'Handle',
    'translate.emailNotifications.none' => 'No email notifications exist yet.',
    'translate.emailNotifications.notFound' => 'Email notification not found.',
    'translate.emailNotifications.edit' => 'Edit email notification',
    'translate.emailNotifications.new' => 'Create a new email notification',
    'translate.emailNotifications.save.error' => 'Couldn’t save email notification.',
    'translate.emailNotifications.save.success' => 'Email notification saved.',
    'translate.emailNotifications.delete' => 'Delete',
    'translate.emailNotifications.deleteMessage' => 'Are you sure you want to delete email notification “{name}”?',

    // ------------------
    // ----- Fields -----
    // ------------------

    'translate.fields.eventTicketTypeMapping.displayName' => 'Available Tickets',
    'translate.fields.eventTicketTypeMapping.addType' => 'Add a ticket type',

    'translate.fields.event.displayName' => 'Event',
    'translate.fields.event.addType' => 'Add a event',
];
