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
use craft\elements\actions\Restore;
use craft\elements\actions\SetStatus;
use craft\elements\actions\View;
use craft\elements\db\ElementQueryInterface;
use craft\elements\User;
use craft\helpers\UrlHelper;
use DateTime;
use fredmansky\eventsky\elements\db\TicketQuery;
use fredmansky\eventsky\models\TicketType;
use yii\db\Exception;

/**
 * Ticket represents a ticket element.
 *
 * @property User|null $author the entry's author
 * @author Fredmanksy GmbH
 */
class Ticket extends Element
{
    // Properties
    // =========================================================================

    /**
     * @var int|null Ticket Type ID
     */
    public $typeId;

    /**
     * @var int|null Event ID
     */
    public $eventId;

    /**
     * @var int|null Author ID
     */
    public $authorId;

    /**
     * @var string Title
     */
    public $title;

    /**
     * @var string Description
     */
    public $description;

    /**
     * @var User|null
     */
    private $_author;

    /** @var \DateTime */
    public $startDate;

    /** @var \DateTime */
    public $endDate;

    /** @var \DateTime */
    // public $dateCreated;

    /** @var \DateTime */
    // public $dateUpdated;

    // Constants
    // =========================================================================

    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.Ticket.displayName');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.Ticket.pluralDisplayName');
    }

    public static function refHandle()
    {
      return 'ticket';
    }

    /**
     * @inheritdoc
     */
    public static function hasContent(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function hasTitles(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function hasStatuses(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function statuses(): array
    {
      return [
        self::STATUS_ENABLED  => Craft::t('app', 'Enabled'),
        self::STATUS_DISABLED => Craft::t('app', 'Disabled')
      ];
    }


  /**
   * @param int|null $siteId
   * @param int|null $ticketId
   * @param int|null $typeId
   * @param int|null $eventId
   * @param string|null $description
   * @param DateTime|null $startDate
   * @param DateTime|null $endDate
   * @return Ticket
   * @throws \Exception
   */
    public static function create(int $siteId = null, int $ticketId = null, int $typeId = null, int $eventId = null, string $description = null, dateTime $startDate = null, dateTime $endDate = null): Ticket
    {
      $element                = new self();
      $element->typeId        = $typeId ?? TicketType::getInstance()->ticketTypes->getTicketTypeById();
      $element->eventId       = $eventId ?? Event::getInstance()->events->getEventById();
      $element->authorId      = \Craft::$app->user->getId();
      $element->description   = $description;
      $element->startDate     = $startDate;
      $element->endDate       = $endDate;

      return $element;
    }


    /**
     * @inheritdoc
     * @return TicketQuery The newly created [[TicketQuery]] instance.
     */
    public static function find(): ElementQueryInterface
    {
        return new TicketQuery(static::class);
    }

    /**
     * @inheritdoc
     */
    protected static function defineSources(string $context = null): array
    {
        return [
            [
                'key'       => '*',
                'label'     =>  Craft::t('eventsky', 'translate.elements.Ticket.sideBar.allTickets'),
                'criteria'  => []
            ],
        ];
    }

    /**
      * @inheritdoc
      */
    protected static function defineActions(string $source = null): array
    {
        $actions = [];
        $elementsService = Craft::$app->getElements();

        // Edit
        $actions[] = $elementsService->createAction([
            'type'                  => Edit::class,
            'label'                 => Craft::t('app', 'Edit entry'),
        ]);

        // View
        $actions[] = $elementsService->createAction([
            'type'                  => View::class,
            'label'                 => Craft::t('app', 'View entry'),
        ]);

        // Set status
        $actions[] = [
            'type'                  => SetStatus::class,
            'allowDisabledForSite'  => true,
        ];

        // Restore
        $actions[] = $elementsService->createAction([
            'type'                  => Restore::class,
            'successMessage'        => Craft::t('app', 'Entries restored.'),
            'partialSuccessMessage' => Craft::t('app', 'Some entries restored.'),
            'failMessage'           => Craft::t('app', 'Entries not restored.'),
        ]);

        // Delete
        $actions[] = $elementsService->createAction([
          'type'                    => Delete::class,
          'confirmationMessage'     => Craft::t('app', 'Are you sure you want to delete the selected entries?'),
          'successMessage'          => Craft::t('app', 'Entries deleted.'),
        ]);

        return $actions;
    }

    /**
     * @inheritdoc
     */
    protected static function defineSortOptions(): array
    {
        return [
          'title'       => \Craft::t('app', 'Title'),
          'event'       => \Craft::t('eventsky', 'EVENT'),
          'ticketType'  => \Craft::t('eventsky', 'TICKET TYPE'),
          'createdAt'   => \Craft::t('eventsky', 'CREATED AT'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected static function defineTableAttributes(): array
    {
        return [
          'title'       => \Craft::t('app', 'Title'),
          'event'       => \Craft::t('eventsky', 'EVENT'),
          'ticketType'  => \Craft::t('eventsky', 'TICKET TYPE'),
          'createdAt'   => \Craft::t('eventsky', 'CREATED AT'),
          'ticketId'    => \Craft::t('eventsky', 'TICKET ID'),
        ];
    }

      /**
       * @return array
       */
      protected static function defineSearchableAttributes(): array
      {
        return ['id', 'title', 'event', 'startDate', 'ticketType'];
      }

     // Public Methods
     // =========================================================================

     /**
      * @inheritdoc
      */
     public function getIsEditable(): bool
     {
         return \Craft::$app->user->checkPermission('edit-ticket:'.$this->getType()->id);
     }

      public function getCpEditUrl()
      {
        return UrlHelper::cpUrl('eventsky/tickets/' . $this->id);
      }


    /*
     * @inheritdoc
     */
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
    }

    /**
     * @param bool $isNew
     * @throws Exception
     */
    public function afterSave(bool $isNew)
    {
        $insertData = [
          'typeId'        => $this->typeId,
          'eventId'       => $this->eventId,
          'authorId'      => $this->authorId,
          'description'   => $this->description,
          'startDate'     => $this->startDate->toDateTimeString(),
          'endDate'       => $this->endDate->toDateTimeString(),
        ];

        if ($isNew) {
            $insertData['id'] = $this->id;

            \Craft::$app->db->createCommand()
              ->insert('{{%eventsky_tickets}}', $insertData)
              ->execute();
        } else {
            \Craft::$app->db->createCommand()
              ->update('{{%eventsky_tickets}}', $insertData, ['id' => $this->id])
              ->execute();
        }

        parent::afterSave($isNew);
    }

    // Protected Methods
    // =========================================================================

    /*
    public function getFieldLayout()
    {
      if ($this->ticketId) {
        return $this->getTicket()->getFieldLayout();
      }

      return null;
    }*/

    /*
  protected function route()
  {
    if (!$this->enabled) {
      return null;
    }

    // Make sure the section is set to have URLs for this site
    $siteId       = \Craft::$app->getSites()->getCurrentSite()->id;
    $siteSettings = $this->getCalendar()->getSiteSettingsForSite($siteId);

    if (!isset($siteSettings) || !$siteSettings->hasUrls) {
      return null;
    }

    return [
      'templates/render',
      [
        'template'  => $siteSettings->template,
        'variables' => [
          'event' => $this,
        ],
      ],
    ];
  }*/

}
