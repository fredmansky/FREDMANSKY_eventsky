<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\models;

use Craft;
use craft\base\Model;
use craft\behaviors\FieldLayoutBehavior;
use craft\gql\types\DateTime;
use craft\helpers\UrlHelper;
use craft\validators\HandleValidator;
use craft\validators\UniqueValidator;
use fredmansky\eventsky\elements\Event;
use yii\base\InvalidConfigException;

/**
 * EntryType model class.
 *
 * @mixin FieldLayoutBehavior
 * @author Fredmansky
 * @since 3.0
 *
 * @property string $cpEditUrl
 */
class EventTypeSite extends Model
{
    public $id;
    public $siteId;
    public $eventtypeId;
    public $hasUrls;
    public $uriFormat;
    public $template;
    public $enabledByDefault;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    /** @var EventType */
    private $eventType;

    public function getEventType(): EventType
    {
        if ($this->eventType !== null) {
            return $this->eventType;
        }

        if (!$this->eventtypeId) {
            throw new InvalidConfigException('Event type site model is missing its event type ID');
        }

        if (($this->eventType = Eventsky::$plugin->eventType->getEventTypeById($this->eventTypeId)) === null) {
            throw new InvalidConfigException('Invalid event type ID: ' . $this->eventTypeId);
        }

        return $this->eventType;
    }

    public function setEventType(EventType $eventType)
    {
        $this->eventType = $eventType;
    }
}
