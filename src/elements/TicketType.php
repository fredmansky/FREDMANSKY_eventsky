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
use craft\elements\actions\View;
use craft\elements\db\ElementQueryInterface;
use craft\elements\User;
use craft\helpers\UrlHelper;
use fredmansky\eventsky\elements\db\TicketTypeQuery;

/**
 * Ticket represents a ticket element.
 *
 * @property User|null $author the entry's author
 * @author Fredmanksy GmbH
 */
class TicketType extends Element
{

    // Properties
    // =========================================================================

    public $id;

    /**
     * @var string Description
     */
    public $description;


    // Constants
    // =========================================================================

    // Static
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.TicketType.displayName');
    }

    public static function pluralDisplayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.TicketType.pluralDisplayName');
    }

    public static function refHandle()
    {
      return 'ticketType';
    }

    public static function hasContent(): bool
    {
        return true;
    }

    public static function hasTitles(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     * @return TicketTypeQuery The newly created [[TicketTypeQuery]] instance.
     */
    public static function find(): ElementQueryInterface
    {
        return new TicketTypeQuery(static::class);
    }

    protected static function defineSources(string $context = null): array
    {
        return [
            [
                'key'       => '*',
                'label'     =>  Craft::t('eventsky', 'translate.elements.TicketType.sideBar.allTicketTypes'),
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

      // Delete
      $actions[] = $elementsService->createAction([
        'type'                    => Delete::class,
        'confirmationMessage'     => Craft::t('app', 'Are you sure you want to delete the selected entries?'),
        'successMessage'          => Craft::t('app', 'Entries deleted.'),
      ]);

      return $actions;
    }

    protected static function defineSortOptions(): array
    {
        return [
          'id'    => \Craft::t('app', 'ID'),
          'title' => \Craft::t('app', 'Title'),
        ];
    }

    protected static function defineTableAttributes(): array
    {
        return [
          'title'         => \Craft::t('app', 'Title'),
          'description'   => \Craft::t('eventsky', 'DESCRIPTION'),
        ];
    }

     // Public Methods
     // =========================================================================

    public function getCpEditUrl()
    {
      return UrlHelper::cpUrl('eventsky/ticket-types/' . $this->id);
    }

    public function afterSave(bool $isNew)
    {

      $insertData = [
        'description'   => $this->description,
      ];

      if ($isNew) {
        $insertData['id'] = $this->id;

        \Craft::$app->db->createCommand()
          ->insert('{{%eventsky_ticketTypes}}', $insertData)
          ->execute();
        } else {
          \Craft::$app->db->createCommand()
            ->update('{{%eventsky_ticketTypes}}', $insertData, ['id' => $this->id])
            ->execute();
        }

        parent::afterSave($isNew);
    }
}
