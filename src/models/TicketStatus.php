<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\models;

use Craft;
use craft\base\Model;
use craft\behaviors\FieldLayoutBehavior;
use craft\helpers\ArrayHelper;
use craft\helpers\UrlHelper;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;

/**
 * EntryType model class.
 *
 * @mixin FieldLayoutBehavior
 * @author Fredmansky
 * @since 3.0
 *
 * @property string $cpEditUrl
 */
class TicketStatus extends Model
{
    public $id;
    public $name;
    public $handle;
    public $color;
    public $dateCreated;
    public $dateUpdated;
    public $dateDeleted;
    public $uid;

    public function __toString(): string
    {
        return (string)$this->handle ?: static::class;
    }

    public function getTicketCountForEvent($eventId)
    {
        return Eventsky::$plugin->ticket->getTicketCountByEventAndStatus($eventId, $this->id);
    }
}
