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
use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\Eventsky;

/**
 * TicketType model class.
 *
 * @mixin FieldLayoutBehavior
 * @author Fredmansky
 * @since 3.0
 *
 * @property string $cpEditUrl
 */
class TicketType extends Model
{
    // Properties
    // =========================================================================

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
                'elementType' => Ticket::class
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
        return UrlHelper::cpUrl('eventsky/tickettype/' . $this->id);
    }
}
