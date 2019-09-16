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
use fredmansky\eventsky\elements\db\EventQuery;
use yii\db\Exception;

/**
 * Event represents an event element.
 *
 * @author Fredmanksy GmbH
 */
class Event extends Element
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
        return Craft::t('eventsky', 'translate.elements.Event.displayName');
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return Craft::t('eventsky', 'translate.elements.Event.pluralDisplayName');
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

    // /**
    //  * @inheritdoc
    //  */
    // public static function hasUris(): bool
    // {
    //     return true;
    // }

    // /**
    //  * @inheritdoc
    //  */
    // public static function isLocalized(): bool
    // {
    //     return true;
    // }

    /**
     * @inheritdoc
     */
    public static function hasStatuses(): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     * @return EventQuery The newly created [[EventQuery]] instance.
     */
    public static function find(): ElementQueryInterface
    {
        return new EventQuery(static::class);
    }

    /**
     * @inheritdoc
     */
    protected static function defineSources(string $context = null): array
    {
        return [
            [
                'key' => '*',
                'label' =>  Craft::t('eventsky', 'translate.elements.Event.sideBar.allEvents'),
                'criteria' => []
            ],
//            [
//                'key' => 'cad',
//                'label' => 'CAD',
//                'criteria' => [
//                    'currency' => 'cad',
//                ]
//            ],
//            [
//                'key' => 'usd',
//                'label' => 'USD',
//                'criteria' => [
//                    'currency' => 'usd',
//                ]
//            ],
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
      * @var DateTime|null Post date
      */
     public $postDate;

     /**
      * @var DateTime|null Expiry date
      */
     public $expiryDate;

     /**
      * @var User|null
      */
     private $_author;

     // Public Methods
     // =========================================================================

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

//     /**
//      * @inheritdoc
//      */
//     public function datetimeAttributes(): array
//     {
//         $attributes = parent::datetimeAttributes();
//         $attributes[] = 'postDate';
//         $attributes[] = 'expiryDate';
//         return $attributes;
//     }

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

//     /**
//      * @inheritdoc
//      */
//     public function getFieldLayout()
//     {
//         return parent::getFieldLayout() ?? $this->getType()->getFieldLayout();
//     }

//     /**
//      * Returns the entry's section.
//      *
//      * ---
//      * ```php
//      * $section = $entry->section;
//      * ```
//      * ```twig
//      * {% set section = entry.section %}
//      * ```
//      *
//      * @return Section
//      * @throws InvalidConfigException if [[sectionId]] is missing or invalid
//      */
//     public function getSection(): Section
//     {
//         if ($this->sectionId === null) {
//             throw new InvalidConfigException('Entry is missing its section ID');
//         }

//         if (($section = Craft::$app->getSections()->getSectionById($this->sectionId)) === null) {
//             throw new InvalidConfigException('Invalid section ID: ' . $this->sectionId);
//         }

//         return $section;
//     }

//     /**
//      * Returns the entry type.
//      *
//      * ---
//      * ```php
//      * $entryType = $entry->type;
//      * ```
//      * ```twig{1}
//      * {% switch entry.type.handle %}
//      *     {% case 'article' %}
//      *         {% include "news/_article" %}
//      *     {% case 'link' %}
//      *         {% include "news/_link" %}
//      * {% endswitch %}
//      * ```
//      *
//      * @return EntryType
//      * @throws InvalidConfigException if [[typeId]] is missing or invalid
//      */
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

//     /**
//      * Returns the entry's author.
//      *
//      * ---
//      * ```php
//      * $author = $entry->author;
//      * ```
//      * ```twig
//      * <p>By {{ entry.author.name }}</p>
//      * ```
//      *
//      * @return User|null
//      * @throws InvalidConfigException if [[authorId]] is set but invalid
//      */
//     public function getAuthor()
//     {
//         if ($this->_author !== null) {
//             return $this->_author;
//         }

//         if ($this->authorId === null) {
//             return null;
//         }

//         if (($this->_author = Craft::$app->getUsers()->getUserById($this->authorId)) === null) {
//             throw new InvalidConfigException('Invalid author ID: ' . $this->authorId);
//         }

//         return $this->_author;
//     }

//     /**
//      * Sets the entry's author.
//      *
//      * @param User|null $author
//      */
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
         return \Craft::$app->user->checkPermission('edit-event:'.$this->getType()->id);
     }

//     /**
//      * @inheritdoc
//      *
//      * ---
//      * ```php
//      * $url = $entry->cpEditUrl;
//      * ```
//      * ```twig{2}
//      * {% if entry.isEditable %}
//      *     <a href="{{ entry.cpEditUrl }}">Edit</a>
//      * {% endif %}
//      * ```
//      */
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
                ->insert('{{%eventsky_events}}', [
                    'id' => $this->id,
                    'description' => $this->description,
                ])
                ->execute();
        } else {
            \Craft::$app->db->createCommand()
                ->update('{{%eventsky_events}}', [
                    'description' => $this->description,
                ], ['id' => $this->id])
                ->execute();
        }

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

//     /**
//      * @inheritdoc
//      */
//     public function afterMoveInStructure(int $structureId)
//     {
//         // Was the entry moved within its section's structure?
//         $section = $this->getSection();

//         if ($section->type == Section::TYPE_STRUCTURE && $section->structureId == $structureId) {
//             Craft::$app->getElements()->updateElementSlugAndUri($this, true, true, true);
//         }

//         parent::afterMoveInStructure($structureId);
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

//     /**
//      * Returns whether the entry should be saving revisions on save.
//      *
//      * @return bool
//      */
//     private function _shouldSaveRevision(): bool
//     {
//         return (
//             $this->id &&
//             !$this->propagating &&
//             !$this->resaving &&
//             !$this->getIsDraft() &&
//             !$this->getIsRevision() &&
//             $this->getSection()->enableVersioning
//         );
//     }
}
