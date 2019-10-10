<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\elements;

use Craft;
use craft\base\Element;
use craft\elements\actions\Edit;
use craft\elements\actions\Restore;
use craft\elements\actions\SetStatus;
use craft\elements\actions\View;
use craft\elements\db\ElementQueryInterface;
use craft\elements\User;
use DateTime;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\elements\db\EventQuery;
use fredmansky\eventsky\elements\db\EventTypeQuery;
use yii\db\Exception;

/**
 * Event Type represents an event type element.
 *
 * @author Fredmanksy GmbH
 */
class EventTypeTODELETE extends Element
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
        return Craft::t('eventsky', 'translate.elements.EventType.displayName');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.EventType.pluralDisplayName');
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
     * @return EventQuery The newly created [[EventQuery]] instance.
     */
    public static function find(): ElementQueryInterface
    {
        return new EventTypeQuery(static::class);
    }

    /**
     * @inheritdoc
     */
    protected static function defineSources(string $context = null): array
    {
        return [
            [
                'key' => '*',
                'label' =>  Craft::t('eventsky', 'translate.elements.Event.sideBar.allEventTypes'),
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

        return $actions;
    }

    /**
     * @inheritdoc
     */
    protected static function defineSortOptions(): array
    {
        return [
//            'title' => \Craft::t('app', 'Title'),
            'description' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.elements.Event.search.description')),
        ];
    }

    /**
     * @inheritdoc
     */
    protected static function defineTableAttributes(): array
    {
        return [
            'title' => \Craft::t('app', 'Title'),
            'description' => \Craft::t('eventsky', 'TEST'),
        ];
    }

    /**
     * @var string Description
     */
    public $description;

     /**
      * @inheritdoc
      */
     public function getIsEditable(): bool
     {
         return \Craft::$app->user->checkPermission('edit-eventType:'.$this->getType()->id);
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
                ->insert(Table::EVENT_TYPES, [
                    'id' => $this->id,
                    'description' => $this->description,
                ])
                ->execute();
        } else {
            \Craft::$app->db->createCommand()
                ->update(Table::EVENT_TYPES, [
                    'description' => $this->description,
                ], ['id' => $this->id])
                ->execute();
        }

        parent::afterSave($isNew);
    }
}
