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
use DateTime;
use fredmansky\eventsky\elements\db\TicketQuery;
use yii\db\Exception;

/**
 * Ticket represents a ticket element.
 *
 * @property User|null $author the entry's author
 * @author Fredmanksy GmbH
 */
class Ticket extends Element
{

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
        self::STATUS_ENABLED => Craft::t('app', 'Enabled'),
        self::STATUS_DISABLED => Craft::t('app', 'Disabled')
      ];
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
                'key' => '*',
                'label' =>  Craft::t('eventsky', 'translate.elements.Ticket.sideBar.allTickets'),
                'criteria' => []
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

        // Delete
        $actions[] = $elementsService->createAction([
          'type' => Delete::class,
          'confirmationMessage' => Craft::t('app', 'Are you sure you want to delete the selected entries?'),
          'successMessage' => Craft::t('app', 'Entries deleted.'),
        ]);

        return $actions;
    }

    /**
     * @inheritdoc
     */
    protected static function defineSortOptions(): array
    {
        return [
          'title' => \Craft::t('app', 'Title'),
          'event' => \Craft::t('eventsky', 'EVENT'),
          'ticketType' => \Craft::t('eventsky', 'TICKET TYPE'),
          'createdAt' => \Craft::t('eventsky', 'CREATED AT'),
        ];
    }

    /**
     * @inheritdoc
     */
    protected static function defineTableAttributes(): array
    {
        return [
          'title' => \Craft::t('app', 'Title'),
          'event' => \Craft::t('eventsky', 'EVENT'),
          'ticketType' => \Craft::t('eventsky', 'TICKET TYPE'),
          'createdAt' => \Craft::t('eventsky', 'CREATED AT'),
          'ticketId' => \Craft::t('eventsky', 'TICKET ID'),
        ];
    }

    // Properties
    // =========================================================================

    /**
     * @var string Description
     */
    public $description;

    /**
     * @var int|null Type ID
     */
    public $typeId;

    /**
     * @var int|null Author ID
     */
    public $authorId;

     /**
      * @var User|null
      */
     private $_author;

     // Public Methods
     // =========================================================================

     /**
      * @inheritdoc
      */
     public function getIsEditable(): bool
     {
         return \Craft::$app->user->checkPermission('edit-ticket:'.$this->getType()->id);
     }


    /*
     * @inheritdoc
     */
    public function getEditorHtml(): string
    {
        $html = \Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
            [
                'label' => \Craft::t('app', 'Title'),
                'siteId' => $this->siteId,
                'id' => 'title',
                'name' => 'title',
                'value' => $this->title,
                'errors' => $this->getErrors('title'),
                'first' => true,
                'autofocus' => true,
                'required' => true
            ]
        ]);

        // ...

        $html .= parent::getEditorHtml();

        return $html;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function afterSave(bool $isNew)
    {
        if ($isNew) {
            \Craft::$app->db->createCommand()
                ->insert('{{%eventsky_tickets}}', [
                    'id' => $this->id,
                    'description' => $this->description,
                ])
                ->execute();
        } else {
            \Craft::$app->db->createCommand()
                ->update('{{%eventsky_tickets}}', [
                    'description' => $this->description,
                ], ['id' => $this->id])
                ->execute();
        }

        parent::afterSave($isNew);
    }
}
