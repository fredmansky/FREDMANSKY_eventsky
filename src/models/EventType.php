<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\models;

use Craft;
use craft\base\Model;
use craft\behaviors\FieldLayoutBehavior;
use craft\gql\types\DateTime;
use craft\helpers\ArrayHelper;
use craft\helpers\UrlHelper;
use craft\validators\HandleValidator;
use craft\validators\UniqueValidator;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;

/**
 * EntryType model class.
 *
 * @mixin FieldLayoutBehavior
 * @author Fredmansky
 * @since 3.0
 *
 * @property string $cpEditUrl
 */
class EventType extends Model
{
    public $id;
    public $fieldLayoutId;
    public $name;
    public $handle;
    public $uid;
    public $isRegistrationEnabled;
    public $isWaitingListEnabled;
    public $dateCreated;
    public $dateUpdated;

    private $eventTypeSites;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'fieldLayout' => [
                'class' => FieldLayoutBehavior::class,
                'elementType' => Event::class
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'handle' => Craft::t('app', 'Handle'),
            'name' => Craft::t('app', 'Name'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
//        $rules[] = [['id', 'fieldLayoutId'], 'number', 'integerOnly' => true];
//        $rules[] = [['name', 'handle'], 'required'];
//        $rules[] = [['name', 'handle'], 'string', 'max' => 255];
//        $rules[] = [
//            ['handle'],
//            HandleValidator::class,
//            'reservedWords' => ['id', 'dateCreated', 'dateUpdated', 'uid', 'title']
//        ];
//        $rules[] = [
//            ['name'],
//            UniqueValidator::class,
//            'targetClass' => EntryTypeRecord::class,
//            'targetAttribute' => ['name', 'sectionId'],
//            'comboNotUnique' => Craft::t('yii', '{attribute} "{value}" has already been taken.'),
//        ];
//        $rules[] = [
//            ['handle'],
//            UniqueValidator::class,
//            'targetClass' => EntryTypeRecord::class,
//            'targetAttribute' => ['handle', 'sectionId'],
//            'comboNotUnique' => Craft::t('yii', '{attribute} "{value}" has already been taken.'),
//        ];

        return $rules;
    }

    /**
     * Use the handle as the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->handle ?: static::class;
    }

    /**
     * Returns the entry’s CP edit URL.
     *
     * @return string
     */
    public function getCpEditUrl(): string
    {
        return UrlHelper::cpUrl('eventsky/eventtype/' . $this->id);
    }


    /**
     * @return EventTypeSite[]
     */
    public function getEventTypeSites(): array
    {
        if ($this->eventTypeSites !== null) {
            return $this->eventTypeSites;
        }

        if (!$this->id) {
            return [];
        }
        $eventTypeSites = Eventsky::$plugin->eventType->getEventTypeSites($this->id);
        $this->setEventTypeSites($eventTypeSites);
        return $this->eventTypeSites;
    }

    public function setEventTypeSites(array $eventTypeSites)
    {
        $this->eventTypeSites = ArrayHelper::index($eventTypeSites, 'siteId');

        /** @var EventTypeSite $eventTypeSite */
        foreach ($this->eventTypeSites as $eventTypeSite) {
            $eventTypeSite->setEventType($this);
        }
    }
}
