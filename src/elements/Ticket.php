<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\elements;

use Craft;
use craft\base\Element;
use craft\elements\actions\Edit;
use craft\elements\actions\NewChild;
use craft\elements\actions\Restore;
use craft\elements\actions\SetStatus;
use craft\elements\actions\View;
use craft\elements\db\ElementQueryInterface;
use craft\elements\User;
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

    public $description;
    public $typeId;
    public $startDate;
    public $endDate;
    public $postDate;
    public $expiryDate;

    public static function displayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.Ticket.displayName');
    }

    static function pluralDisplayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.Ticket.pluralDisplayName');
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

    protected static function defineSources(string $context = null): array
    {
      $sources = [
        [
          'key' => '*',
          'label' =>  Craft::t('eventsky', 'translate.elements.Ticket.sideBar.allTickets'),
          'criteria' => []
        ],
        $sources[] = ['heading' => Craft::t('eventsky', 'translate.elements.Ticket.sideBar.ticketTypeHeading')],
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

      // Set status
      $actions[] = [
        'type' => SetStatus::class,
        'allowDisabledForSite' => true,
      ];

      // Restore
      $actions[] = $elementsService->createAction([
        'type' => Restore::class,
        'successMessage' => Craft::t('app', 'Entries restored.'),
        'partialSuccessMessage' => Craft::t('app', 'Some entries restored.'),
        'failMessage' => Craft::t('app', 'Entries not restored.'),
      ]);

      return $actions;
    }

    protected static function defineSortOptions(): array
    {
        return [
          'title'       => \Craft::t('app', 'Title'),
          'description' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.elements.Ticket.search.description')),
        ];
    }

    protected static function defineTableAttributes(): array
    {
        return [
          'title'       => \Craft::t('app', 'Title'),
          'event'       => \Craft::t('eventsky', 'EVENT'),
          'ticketType'  => \Craft::t('eventsky', 'TICKET TYPE'),
          'ticketId'    => \Craft::t('eventsky', 'TICKET ID'),
        ];
    }

    protected static function defineSearchableAttributes(): array
    {
      return ['id', 'title', 'ticketType'];
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

/*    public function getCpEditUrl()
    {
      return UrlHelper::cpUrl('eventsky/tickets/' . $this->id);
    }


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
      $record->description = $this->description;
      $record->startDate = $this->startDate;
      $record->endDate = $this->endDate;
      $record->postDate = $this->postDate;
      $record->expiryDate = $this->expiryDate;

      $record->save(false);

      $this->id = $record->id;

      parent::afterSave($isNew);
    }

    private function _shouldSaveRevision(): bool
    {
      return false;
    }
}
