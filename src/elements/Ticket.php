<?php
/**
 * Eventsky plugin for Craft CMS 3.x
 *
 * Craft plugin for event management and attendee registration
 *
 * @link      https://fredmansky.at
 * @copyright Copyright (c) 2021 Fredmansky
 */

namespace fredmansky\eventsky\elements;

use Craft;
use craft\base\Element;

class Ticket extends Element
{
    // Public Properties
    // =========================================================================

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.Ticket.displayName');
    }

    public static function pluralDisplayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.Ticket.pluralDisplayName');
    }
}
