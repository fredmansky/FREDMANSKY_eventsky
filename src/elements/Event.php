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
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\elements\db\EventQuery;
use fredmansky\eventsky\Eventsky;
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

    public $description;
    public $typeId;
//    public $authorId;
    public $startDate;
    public $endDate;
    public $postDate;
    public $expiryDate;
//    private $_author;

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

    // public static function isLocalized(): bool
    // {
    //     return true;
    // }

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

        // Create
        $newChildUrl = 'eventsky/event/new';

//        if (Craft::$app->getIsMultiSite()) {
//            $newChildUrl .= '?site=' . $site->handle;
//        }

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
//            'title' => \Craft::t('app', 'Title'),
            'description' => \Craft::t('eventsky', Craft::t('eventsky', 'translate.elements.Event.search.description')),
        ];
    }

    protected static function defineTableAttributes(): array
    {
        return [
            'title' => \Craft::t('app', 'Title'),
            'description' => \Craft::t('eventsky', 'TEST'),
        ];
    }

    // /**
    //  * @inheritdoc
    //  */
    // public static function eagerLoadingMap(array $sourceElements, string $handle)
    // {
    //     if ($handle === 'author') {
    //         // Get the source element IDs
    //         $sourceElementIds = ArrayHelper::getColumn($sourceElements, 'id');

    //         $map = (new Query())
    //             ->select(['id as source', 'authorId as target'])
    //             ->from([Table::ENTRIES])
    //             ->where(['and', ['id' => $sourceElementIds], ['not', ['authorId' => null]]])
    //             ->all();

    //         return [
    //             'elementType' => User::class,
    //             'map' => $map
    //         ];
    //     }

    //     return parent::eagerLoadingMap($sourceElements, $handle);
    // }

    // /**
    //  * @inheritdoc
    //  */
    // protected static function prepElementQueryForTableAttribute(ElementQueryInterface $elementQuery, string $attribute)
    // {
    //     if ($attribute === 'author') {
    //         $elementQuery->andWith('author');
    //     } else {
    //         parent::prepElementQueryForTableAttribute($elementQuery, $attribute);
    //     }
    // }




//     /**
//      * @inheritdoc
//      */
//     public function __clone()
//     {
//         parent::__clone();
//         $this->_hasNewParent = null;
//     }

//     /**
//      * @inheritdoc
//      */
//     public function extraFields()
//     {
//         $names = parent::extraFields();
//         $names[] = 'author';
//         $names[] = 'section';
//         $names[] = 'type';
//         return $names;
//     }

    public function datetimeAttributes(): array
    {
        $attributes = parent::datetimeAttributes();
        $attributes[] = 'postDate';
        $attributes[] = 'expiryDate';
        $attributes[] = 'startDate';
        $attributes[] = 'endDate';
        return $attributes;
    }

//     /**
//      * @inheritdoc
//      */
//     public function attributeLabels()
//     {
//         $labels = parent::attributeLabels();

//         // Use the entry type's title label
//         if ($titleLabel = $this->getType()->titleLabel) {
//             $labels['title'] = Craft::t('site', $titleLabel);
//         }

//         return $labels;
//     }

//     /**
//      * @inheritdoc
//      */
//     public function rules()
//     {
//         $rules = parent::rules();
//         $rules[] = [['sectionId', 'typeId', 'authorId', 'newParentId'], 'number', 'integerOnly' => true];
//         $rules[] = [['postDate', 'expiryDate'], DateTimeValidator::class];

//         if ($this->getSection()->type !== Section::TYPE_SINGLE) {
//             $rules[] = [['authorId'], 'required', 'on' => self::SCENARIO_LIVE];
//         }

//         return $rules;
//     }

//     /**
//      * @inheritdoc
//      */
//     public function getSupportedSites(): array
//     {
//         $section = $this->getSection();
//         /** @var Site[] $allSites */
//         $allSites = ArrayHelper::index(Craft::$app->getSites()->getAllSites(), 'id');
//         $sites = [];

//         foreach ($section->getSiteSettings() as $siteSettings) {
//             switch ($section->propagationMethod) {
//                 case Section::PROPAGATION_METHOD_NONE:
//                     $include = $siteSettings->siteId == $this->siteId;
//                     break;
//                 case Section::PROPAGATION_METHOD_SITE_GROUP:
//                     $include = $allSites[$siteSettings->siteId]->groupId == $allSites[$this->siteId]->groupId;
//                     break;
//                 case Section::PROPAGATION_METHOD_LANGUAGE:
//                     $include = $allSites[$siteSettings->siteId]->language == $allSites[$this->siteId]->language;
//                     break;
//                 default:
//                     $include = true;
//                     break;
//             }

//             if ($include) {
//                 $sites[] = [
//                     'siteId' => $siteSettings->siteId,
//                     'enabledByDefault' => $siteSettings->enabledByDefault
//                 ];
//             }
//         }

//         return $sites;
//     }

//     /**
//      * @inheritdoc
//      * @throws InvalidConfigException if [[siteId]] is not set to a site ID that the entry's section is enabled for
//      */
//     public function getUriFormat()
//     {
//         $sectionSiteSettings = $this->getSection()->getSiteSettings();

//         if (!isset($sectionSiteSettings[$this->siteId])) {
//             throw new InvalidConfigException('Entry’s section (' . $this->sectionId . ') is not enabled for site ' . $this->siteId);
//         }

//         return $sectionSiteSettings[$this->siteId]->uriFormat;
//     }

//     /**
//      * @inheritdoc
//      */
//     protected function route()
//     {
//         // Make sure that the entry is actually live
//         if (!$this->previewing && $this->getStatus() != self::STATUS_LIVE) {
//             return null;
//         }

//         // Make sure the section is set to have URLs for this site
//         $siteId = Craft::$app->getSites()->getCurrentSite()->id;
//         $sectionSiteSettings = $this->getSection()->getSiteSettings();

//         if (!isset($sectionSiteSettings[$siteId]) || !$sectionSiteSettings[$siteId]->hasUrls) {
//             return null;
//         }

//         return [
//             'templates/render', [
//                 'template' => $sectionSiteSettings[$siteId]->template,
//                 'variables' => [
//                     'entry' => $this,
//                 ]
//             ]
//         ];
//     }

//     /**
//      * @inheritdoc
//      */
//     protected function previewTargets(): array
//     {
//         return $this->getSection()->previewTargets;
//     }


//     public function getFieldLayout()
//     {
//         return $this->getType()->getFieldLayout();
//     }
//
//     public function getSection(): Section
//     {
//         if ($this->sectionId === null) {
//             throw new InvalidConfigException('Entry is missing its section ID');
//         }
//
//         if (($section = Craft::$app->getSections()->getSectionById($this->sectionId)) === null) {
//             throw new InvalidConfigException('Invalid section ID: ' . $this->sectionId);
//         }
//
//         return $section;
//     }

//     public function getType(): EntryType
//     {
//         if ($this->typeId === null) {
//             throw new InvalidConfigException('Entry is missing its type ID');
//         }

//         $sectionEntryTypes = ArrayHelper::index($this->getSection()->getEntryTypes(), 'id');

//         if (!isset($sectionEntryTypes[$this->typeId])) {
//             throw new InvalidConfigException('Invalid entry type ID: ' . $this->typeId);
//         }

//         return $sectionEntryTypes[$this->typeId];
//     }

//     public function getAuthor()
//     {
//         if ($this->_author !== null) {
//             return $this->_author;
//         }
//
//         if ($this->authorId === null) {
//             return null;
//         }
//
//         if (($this->_author = Craft::$app->getUsers()->getUserById($this->authorId)) === null) {
//             throw new InvalidConfigException('Invalid author ID: ' . $this->authorId);
//         }
//
//         return $this->_author;
//     }

//     public function setAuthor(User $author = null)
//     {
//         $this->_author = $author;
//     }

//     /**
//      * @inheritdoc
//      */
//     public function getStatus()
//     {
//         $status = parent::getStatus();

//         if ($status == self::STATUS_ENABLED && $this->postDate) {
//             $currentTime = DateTimeHelper::currentTimeStamp();
//             $postDate = $this->postDate->getTimestamp();
//             $expiryDate = ($this->expiryDate ? $this->expiryDate->getTimestamp() : null);

//             if ($postDate <= $currentTime && ($expiryDate === null || $expiryDate > $currentTime)) {
//                 return self::STATUS_LIVE;
//             }

//             if ($postDate > $currentTime) {
//                 return self::STATUS_PENDING;
//             }

//             return self::STATUS_EXPIRED;
//         }

//         return $status;
//     }

     /**
      * @inheritdoc
      */
     public function getIsEditable(): bool
     {
         return true;
//         return \Craft::$app->user->checkPermission('edit-event:'.$this->getType()->id);
     }

//     public function getCpEditUrl()
//     {
//         $section = $this->getSection();

//         // The slug *might* not be set if this is a Draft and they've deleted it for whatever reason
//         $path = 'entries/' . $section->handle . '/' . $this->getSourceId() .
//             ($this->slug && strpos($this->slug, '__') !== 0 ? '-' . $this->slug : '');

//         $params = [];
//         if (Craft::$app->getIsMultiSite()) {
//             $params['site'] = $this->getSite()->handle;
//         }
//         if ($this->getIsDraft()) {
//             $params['draftId'] = $this->draftId;
//         }

//         return UrlHelper::cpUrl($path, $params);
//     }

//     /**
//      * @inheritdoc
//      */
//     public function setEagerLoadedElements(string $handle, array $elements)
//     {
//         if ($handle === 'author') {
//             $author = $elements[0] ?? null;
//             $this->setAuthor($author);
//         } else {
//             parent::setEagerLoadedElements($handle, $elements);
//         }
//     }

//     // Indexes, etc.
//     // -------------------------------------------------------------------------

//     /**
//      * @inheritdoc
//      */
//     protected function tableAttributeHtml(string $attribute): string
//     {
//         switch ($attribute) {
//             case 'author':
//                 $author = $this->getAuthor();
//                 return $author ? Craft::$app->getView()->renderTemplate('_elements/element', ['element' => $author]) : '';

//             case 'section':
//                 return Craft::t('site', $this->getSection()->name);

//             case 'type':
//                 try {
//                     return Craft::t('site', $this->getType()->name);
//                 } catch (InvalidConfigException $e) {
//                     return Craft::t('app', 'Unknown');
//                 }
//         }

//         return parent::tableAttributeHtml($attribute);
//     }


//    public function getEditorHtml(): string
//    {
//        $html = \Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'textField', [
//            [
//                'label' => \Craft::t('app', 'Title'),
//                'siteId' => $this->siteId,
//                'id' => 'title',
//                'name' => 'title',
//                'value' => $this->title,
//                'errors' => $this->getErrors('title'),
//                'first' => true,
//                'autofocus' => true,
//                'required' => true
//            ]
//        ]);
//
//        // ...
//
//        $html .= parent::getEditorHtml();
//
//        return $html;
//    }

    /**
     * @inheritdoc
     * @throws Exception
     */
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
        $record->description = $this->description;
        $record->startDate = $this->startDate;
        $record->endDate = $this->endDate;
        $record->postDate = $this->postDate;
        $record->expiryDate = $this->expiryDate;

        $record->save(false);

        $this->id = $record->id;

        parent::afterSave($isNew);
    }

//     /**
//      * @inheritdoc
//      */
//     public function afterPropagate(bool $isNew)
//     {
//         parent::afterPropagate($isNew);

//         // Save a new revision?
//         if ($this->_shouldSaveRevision()) {
//             Craft::$app->getRevisions()->createRevision($this, $this->revisionCreatorId, $this->revisionNotes);
//         }
//     }

//     /**
//      * @inheritdoc
//      */
//     public function beforeDelete(): bool
//     {
//         if (!parent::beforeDelete()) {
//             return false;
//         }

//         $data = [
//             'deletedWithEntryType' => $this->deletedWithEntryType,
//             'parentId' => null,
//         ];

//         if ($this->structureId) {
//             // Remember the parent ID, in case the entry needs to be restored later
//             $parentId = $this->getAncestors(1)
//                 ->anyStatus()
//                 ->select(['elements.id'])
//                 ->scalar();
//             if ($parentId) {
//                 $data['parentId'] = $parentId;
//             }
//         }

//         Craft::$app->getDb()->createCommand()
//             ->update(Table::ENTRIES, $data, ['id' => $this->id], [], false)
//             ->execute();

//         return true;
//     }

//     /**
//      * @inheritdoc
//      */
//     public function afterRestore()
//     {
//         $section = $this->getSection();
//         if ($section->type === Section::TYPE_STRUCTURE) {
//             // Add the entry back into its structure
//             $parent = self::find()
//                 ->structureId($section->structureId)
//                 ->innerJoin('{{%entries}} j', '[[j.parentId]] = [[elements.id]]')
//                 ->andWhere(['j.id' => $this->id])
//                 ->one();

//             if (!$parent) {
//                 Craft::$app->getStructures()->appendToRoot($section->structureId, $this);
//             } else {
//                 Craft::$app->getStructures()->append($section->structureId, $this, $parent);
//             }
//         }

//         parent::afterRestore();
//     }

//     // Private Methods
//     // =========================================================================

//     /**
//      * Returns whether the entry has been assigned a new parent entry.
//      *
//      * @return bool
//      * @see beforeSave()
//      * @see afterSave()
//      */
//     private function _hasNewParent(): bool
//     {
//         if ($this->_hasNewParent !== null) {
//             return $this->_hasNewParent;
//         }

//         return $this->_hasNewParent = $this->_checkForNewParent();
//     }

//     /**
//      * Checks if the entry has been assigned a new parent entry.
//      *
//      * @return bool
//      * @see _hasNewParent()
//      */
//     private function _checkForNewParent(): bool
//     {
//         // Make sure this is a Structure section
//         if ($this->getSection()->type != Section::TYPE_STRUCTURE) {
//             return false;
//         }

//         // Is it a brand new entry?
//         if ($this->id === null) {
//             return true;
//         }

//         // Was a new parent ID actually submitted?
//         if ($this->newParentId === null) {
//             return false;
//         }

//         // Is it set to the top level now, but it hadn't been before?
//         if (!$this->newParentId && $this->level != 1) {
//             return true;
//         }

//         // Is it set to be under a parent now, but didn't have one before?
//         if ($this->newParentId && $this->level == 1) {
//             return true;
//         }

//         // Is the parentId set to a different entry ID than its previous parent?
//         $oldParentQuery = self::find();
//         $oldParentQuery->ancestorOf($this);
//         $oldParentQuery->ancestorDist(1);
//         $oldParentQuery->siteId($this->siteId);
//         $oldParentQuery->anyStatus();
//         $oldParentQuery->select('elements.id');
//         $oldParentId = $oldParentQuery->scalar();

//         return $this->newParentId != $oldParentId;
//     }

    private function _shouldSaveRevision(): bool
    {
        return false;
    }
}
