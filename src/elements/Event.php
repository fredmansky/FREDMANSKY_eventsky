<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\elements;

use Craft;
use craft\base\Element;
use craft\elements\actions\Delete;
use craft\elements\actions\Edit;
use craft\elements\actions\SetStatus;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Html;
use craft\helpers\UrlHelper;
use DateTime;
use fredmansky\eventsky\elements\db\EventQuery;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\EmailNotification;
use fredmansky\eventsky\models\EventType;
use fredmansky\eventsky\records\EventRecord;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Event represents an event element.
 *
 * @author Fredmanksy GmbH
 */
class Event extends Element
{

    const STATUS_LIVE = 'live';
    const STATUS_PENDING = 'pending';
    const STATUS_EXPIRED = 'expired';

    public $typeId;
    public $startDate;
    public $endDate;
    public $postDate;
    public $expiryDate;
    public $needsRegistration;
    public $registrationEnabled;
    public $totalTickets;
    public $hasWaitingList;
    public $waitingListSize;
    public $emailNotificationIdAdmin;
    public $emailNotificationAdminEmails;

    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public static function displayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.Event.displayName');
    }

    public static function pluralDisplayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.Event.pluralDisplayName');
    }

    public static function hasContent(): bool
    {
        return true;
    }

    public static function hasTitles(): bool
    {
        return true;
    }

    public static function hasUris(): bool
    {
        return true;
    }

    public static function hasStatuses(): bool
    {
        return true;
    }

    public static function find(): ElementQueryInterface
    {
        return new EventQuery(static::class);
    }

    public function getFieldLayout()
    {
        return parent::getFieldLayout() ?? $this->getType()->getFieldLayout();
    }

    protected function normalizeFieldValue(string $fieldHandle)
    {
        if (strcmp('availableTickets', $fieldHandle) === 0) {
            $field = Eventsky::$plugin->fieldService->getFieldByHandle($fieldHandle);

            if (!$field) {
                throw new Exception('Invalid field handle: ' . $fieldHandle);
            }

            $behavior = $this->getBehavior('customFields');
            $behavior->$fieldHandle = $field->normalizeValue($behavior->$fieldHandle, $this);
            return;
        }

        parent::normalizeFieldValue($fieldHandle);
    }

    public function getType(): EventType
    {
        if ($this->typeId === null) {
            throw new InvalidConfigException('Event is missing its type ID');
        }

        $eventType = Eventsky::$plugin->eventType->getEventTypeById($this->typeId);

        if (!$eventType) {
            throw new InvalidConfigException('Invalid event type ID: ' . $this->typeId);
        }

        return $eventType;
    }

    public function getEventTicketTypes(): array
    {
        $availableTickets = Eventsky::$plugin->event->getAllTicketTypeMappingsByEventId($this->id);
        return $availableTickets;
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

    protected static function defineSources(string $context = null): array
    {
        $sources = [
            [
                'key' => '*',
                'label' =>  Craft::t('eventsky', 'translate.elements.Event.sideBar.allEvents'),
                'criteria' => []
            ],
            $sources[] = ['heading' => Craft::t('eventsky', 'translate.elements.Event.sideBar.eventTypeHeading')],
        ];

        $eventTypes = Eventsky::$plugin->eventType->getAllEventTypes();
        foreach ($eventTypes as &$eventType) {
            $sources[] = [
                'key' => $eventType->handle,
                'label' => $eventType->name,
                'criteria' => [
                    'typeId' => $eventType->id,
                ],
            ];
        }

        return $sources;
    }

    protected static function defineActions(string $source = null): array
    {
        $actions = [];
        $elementsService = Craft::$app->getElements();

        $actions[] = $elementsService->createAction([
            'type' => Edit::class,
            'label' => Craft::t('app', 'Edit entry'),
        ]);

        $actions[] = $elementsService->createAction([
            'type' => Delete::class,
            'confirmationMessage' => Craft::t('app', 'Are you sure you want to delete the selected entries?'),
            'successMessage' => Craft::t('app', 'Entries deleted.'),
        ]);

        $actions[] = [
            'type' => SetStatus::class,
            'allowDisabledForSite' => true,
        ];

        return $actions;
    }

    protected static function defineSortOptions(): array
    {
        return [
            'title'       => \Craft::t('app', 'Title'),
            'typeId'      => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.search.ticketType')),
        ];
    }

    protected static function defineTableAttributes(): array
    {
        return [
            'title' => \Craft::t('app', 'Title'),
            'typeId' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.table.typeId')),
            'numberOfRegistrations' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.table.numberOfRegistrations')),
            'totalTickets' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.table.totalTickets')),
            'ticketOverviewUrl' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.events.tickets.ticketOverview')),
        ];
    }

    protected function tableAttributeHtml(string $attribute): string
    {
        if($attribute == 'ticketOverviewUrl') {

            $url = $this->getTicketOverviewUrl();

            if ($url !== null) {
                return Html::a(Craft::t('eventsky', 'translate.events.tickets.ticketLink'), $url);
            }

            return '';
        }

        if($attribute == 'numberOfRegistrations') {
            return count(Eventsky::$plugin->ticket->getTicketsByEvent($this));
        }

        return parent::tableAttributeHtml($attribute);
    }

    public function datetimeAttributes(): array
    {
        $attributes = parent::datetimeAttributes();
        $attributes[] = 'postDate';
        $attributes[] = 'expiryDate';
        $attributes[] = 'startDate';
        $attributes[] = 'endDate';

        return $attributes;
    }

    public function getIsEditable(): bool
    {
        return true;
    }

    public function getCpEditUrl()
    {
        // The slug *might* not be set if this is a Draft and they've deleted it for whatever reason
        $path = 'eventsky/event/' . $this->id .
            ($this->slug && strpos($this->slug, '__') !== 0 ? '-' . $this->slug : '');

        $params = [];
        return UrlHelper::cpUrl($path, $params);
    }

    public function getTicketOverviewUrl()
    {
        $path = 'eventsky/event/' . $this->id .
            ($this->slug && strpos($this->slug, '__') !== 0 ? '-' . $this->slug : '')
            . '/tickets';

        $params = [];
        return UrlHelper::cpUrl($path, $params);
    }

    public function beforeSave(bool $isNew): bool
    {
        $titleFormat = $this->getType()->titleFormat;
        if ($titleFormat) {
            $this->title = Craft::$app->getView()->renderObjectTemplate($titleFormat, $this);
        }

        // Make sure the field layout is set correctly
        $this->fieldLayoutId = $this->getType()->fieldLayoutId;

        if ($this->enabled && !$this->postDate) {
            // Default the post date to the current date/time
            $this->postDate = new DateTime();
            // ...without the seconds
            $this->postDate->setTimestamp($this->postDate->getTimestamp() - ($this->postDate->getTimestamp() % 60));
        }

        return parent::beforeSave($isNew);
    }

    public function afterSave(bool $isNew)
    {
        if (!$isNew) {
            $record = EventRecord::findOne($this->id);

            if (!$record) {
                throw new Exception('Invalid event ID: ' . $this->id);
            }
        } else {
            $record = new EventRecord();
            $record->id = $this->id;
        }

        $record->typeId = $this->typeId;
        $record->startDate = $this->startDate;
        $record->endDate = $this->endDate;
        $record->postDate = $this->postDate;
        $record->expiryDate = $this->expiryDate;
        $record->needsRegistration = $this->needsRegistration;
        $record->registrationEnabled = $this->registrationEnabled;
        $record->totalTickets = $this->totalTickets;
        $record->hasWaitingList = $this->hasWaitingList;
        $record->waitingListSize = $this->waitingListSize;
        $record->emailNotificationIdAdmin = $this->emailNotificationIdAdmin;
        $record->emailNotificationAdminEmails = $this->emailNotificationAdminEmails;

        $record->save(false);

        $this->id = $record->id;

        parent::afterSave($isNew);
    }
}
