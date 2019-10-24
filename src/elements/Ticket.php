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
use craft\elements\actions\NewChild;
use craft\elements\actions\SetStatus;
use craft\elements\actions\View;
use craft\elements\db\ElementQueryInterface;
use craft\elements\User;
use craft\helpers\UrlHelper;
use DateTime;
use fredmansky\eventsky\db\Table;
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
    public $name;
    public $handle;
    public $uid;
    public $description;
    public $typeId;
    public $eventId;
    public $startDate;
    public $endDate;
    public $postDate;
    public $expiryDate;
    public $dateDeleted;

    public static function displayName(): string
    {
        return Craft::t('eventsky', 'translate.tickets.displayName');
    }

    static function pluralDisplayName(): string
    {
        return Craft::t('eventsky', 'translate.tickets.pluralDisplayName');
    }

    public static function refHandle()
    {
      return 'ticket';
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

    public static function statuses(): array
    {
      return [
        self::STATUS_ENABLED => Craft::t('app', 'Enabled'),
        self::STATUS_DISABLED => Craft::t('app', 'Disabled'),
      ];
    }

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

      // Create
      $newChildUrl = 'eventsky/ticket/new';

      $actions[] = $elementsService->createAction([
        'type' => NewChild::class,
        'label' => Craft::t('app', 'Create a new child entry'),
        'newChildUrl' => $newChildUrl,
      ]);

      // Edit
      $actions[] = $elementsService->createAction([
        'type' => Edit::class,
        'label' => Craft::t('app', 'Edit entry'),
      ]);

      // View
      $actions[] = $elementsService->createAction([
        'type' => View::class,
        'label' => Craft::t('app', 'View entry'),
      ]);

      $actions[] = $elementsService->createAction([
        'type' => Delete::class,
        'confirmationMessage' => Craft::t('app', 'Are you sure you want to delete the selected entries?'),
        'successMessage' => Craft::t('app', 'Entries deleted.'),
      ]);

      // Set status
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
          'id'            => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.table.name')),
          'typeId'        => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.table.typeId')),
          'eventId'       => \Craft::t('eventsky', Craft::t('eventsky', 'translate.tickets.table.eventId')),
        ];
    }

    protected static function defineSearchableAttributes(): array
    {
      return ['name', 'typeId'];
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
      // return \Craft::$app->user->checkPermission('edit-ticket:'.$this->getType()->id);
    }

    public function getCpEditUrl(): string
    {
      return UrlHelper::cpUrl('eventsky/ticket/' . $this->id);
    }

/*
    public function getEditorHtml(): string
    {
        $html = \Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label'     => \Craft::t('app', 'Title'),
                'siteId'    => $this->siteId,
                'id'        => 'title',
                'name'      => 'title',
                'value'     => $this->title,
                'errors'    => $this->getErrors('title'),
                'first'     => true,
                'autofocus' => true,
                'required'  => true
            ]
        ]);

        // ...

        $html .= parent::getEditorHtml();

        return $html;
    }*/

    public function beforeSave(bool $isNew): bool
    {
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
      $record->name = $this->name;
      $record->handle = $this->handle;
      $record->description = $this->description;
      $record->startDate = $this->startDate;
      $record->endDate = $this->endDate;
      $record->postDate = $this->postDate;
      $record->expiryDate = $this->expiryDate;
      $record->dateDeleted = $this->dateDeleted;

      $record->save(false);

      $this->id = $record->id;

      parent::afterSave($isNew);
    }

    private function _shouldSaveRevision(): bool
    {
      return false;
    }
}
