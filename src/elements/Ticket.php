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
use craft\elements\User;
use craft\helpers\UrlHelper;
use fredmansky\eventsky\elements\db\TicketQuery;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\TicketType;
use fredmansky\eventsky\records\TicketRecord;
use yii\base\InvalidConfigException;
use yii\db\Exception;


/**
 * Ticket represents a ticket element.
 *
 * @property User|null $author the entry's author
 * @author Fredmanksy GmbH
 */
class Ticket extends Element
{
    public $id;
    public $typeId;
    public $eventId;
    public $statusId;
    public $email;
    public $uid;

    public static function displayName(): string
    {
        return Craft::t('eventsky', 'translate.tickets.displayName');
    }

    static function pluralDisplayName(): string
    {
        return Craft::t('eventsky', 'translate.tickets.pluralDisplayName');
    }

//    public static function refHandle()
//    {
//      return 'ticket';
//    }

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
        return false;
    }

    public static function hasStatuses(): bool
    {
        return true;
    }

//    public static function statuses(): array
//    {
//        return [
//            self::STATUS_ENABLED => Craft::t('app', 'Enabled'),
//            self::STATUS_DISABLED => Craft::t('app', 'Disabled'),
//        ];
//    }

    public static function find(): ElementQueryInterface
    {
        return new TicketQuery(static::class);
    }

    public function getFieldLayout()
    {
        return parent::getFieldLayout() ?? $this->getType()->getFieldLayout();
    }

    public function getType(): TicketType
    {
        if ($this->typeId === null) {
            throw new InvalidConfigException('Ticket is missing its type ID');
        }

        $ticketType = Eventsky::$plugin->ticketType->getTicketTypeById($this->typeId);

        if (!$ticketType) {
            throw new InvalidConfigException('Invalid ticket type ID: ' . $this->typeId);
        }

        return $ticketType;
    }

    public function getEvent(): Event
    {
        if ($this->eventId === null) {
            throw new InvalidConfigException('Ticket is missing its event ID');
        }

        $event = Eventsky::$plugin->event->getEventById($this->eventId);

        if (!$event) {
            throw new InvalidConfigException('Invalid event ID: ' . $this->eventId);
        }

        return $event;
    }

    protected static function defineSources(string $context = null): array
    {
        $sources = [
            [
                'key' => '*',
                'label' =>  Craft::t('eventsky', 'translate.tickets.sideBar.allTickets'),
                'criteria' => []
            ],
            $sources[] = ['heading' => Craft::t('eventsky', 'translate.tickets.sideBar.ticketTypeHeading')],
        ];

        $ticketTypes = Eventsky::$plugin->ticketType->getAllTicketTypes();
        foreach ($ticketTypes as &$ticketType) {
            $sources[] = [
                'key' => $ticketType->handle,
                'label' => $ticketType->name,
                'criteria' => [
                    'typeId' => $ticketType->id,
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
            'title'  => \Craft::t('app', 'Title'),
            'typeId' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.search.ticketType')),
        ];
    }

    protected static function defineTableAttributes(): array
    {
        return [
            'id' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.table.name')),
            'typeId' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.table.typeId')),
            'eventId' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.table.eventId')),
        ];
    }

//    protected static function defineSearchableAttributes(): array
//    {
//        return ['name', 'typeId'];
//    }

    public function getIsEditable(): bool
    {
        return true;
    }

    public function getCpEditUrl(): string
    {
        return UrlHelper::cpUrl('eventsky/ticket/' . $this->id);
    }

    public function beforeSave(bool $isNew): bool
    {
        // Make sure the field layout is set correctly
        $this->fieldLayoutId = $this->getType()->fieldLayoutId;
        return parent::beforeSave($isNew);
    }

    public function afterSave(bool $isNew)
    {
        if (!$isNew) {
            $record = TicketRecord::findOne($this->id);

            if (!$record) {
                throw new Exception('Invalid ticket ID: ' . $this->id);
            }
        } else {
            $record = new TicketRecord();
            $record->id = $this->id;
        }

        $record->typeId = $this->typeId;
        $record->eventId = $this->eventId;
        $record->statusId = $this->statusId;
        $record->email = $this->email;

        $record->save(false);

        $this->id = $record->id;

        parent::afterSave($isNew);
    }

    private function _shouldSaveRevision(): bool
    {
        return false;
    }
}
