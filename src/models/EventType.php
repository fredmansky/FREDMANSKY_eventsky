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
class EventType extends Model
{
    public $id;
    public $name;
    public $handle;
    public $fieldLayoutId;
    public $isRegistrationEnabled;
    public $isWaitingListEnabled;
    public $dateCreated;
    public $dateUpdated;
    public $dateDeleted;
    public $uid;
    public $emailNotificationIdAdmin;
    public $emailNotificationAdminEmails;

    private $eventTypeSites;

    public function behaviors(): array
    {
        return [
            'fieldLayout' => [
                'class' => FieldLayoutBehavior::class,
                'elementType' => Event::class
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'handle' => Craft::t('app', 'Handle'),
            'name' => Craft::t('app', 'Name'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    public function __toString(): string
    {
        return (string)$this->handle ?: static::class;
    }

    public function getCpEditUrl(): string
    {
        return UrlHelper::cpUrl('eventsky/eventtype/' . $this->id);
    }

    public function getEventTypeSites(): array
    {
        if ($this->eventTypeSites !== null) {
            return $this->eventTypeSites;
        }

        if (!$this->id) {
            return [];
        }
        $eventTypeSites = Eventsky::$plugin->eventType->getEventTypeSites($this->id);
        $this->setEventTypeSites($eventTypeSites);
        return $this->eventTypeSites;
    }

    public function setEventTypeSites(array $eventTypeSites)
    {
        $this->eventTypeSites = ArrayHelper::index($eventTypeSites, 'siteId');

        /** @var EventTypeSite $eventTypeSite */
        foreach ($this->eventTypeSites as $eventTypeSite) {
            $eventTypeSite->setEventType($this);
        }
    }

    public function getEmailNotification(): ?EmailNotification
    {
        if ($this->emailNotificationIdAdmin === null) {
            return null;
        }

        $emailNotification = Eventsky::$plugin->emailNotification->getEmailNotificationById($this->emailNotificationIdAdmin);

        if (!$emailNotification) {
            throw new InvalidConfigException('Invalid email notification ID: ' . $this->emailNotificationIdAdmin);
        }

        return $emailNotification;
    }
}
