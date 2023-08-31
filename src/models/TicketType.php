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
use yii\base\InvalidConfigException;

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
    public $id;
    public $name;
    public $handle;
    public $titleFormat;
    public $fieldLayoutId;
    public $uid;
    public $dateCreated;
    public $dateUpdated;
    public $dateDeleted;
    public $emailNotificationIdUser;

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
    public function rules(): array
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

    public function getEmailNotification(): ?EmailNotification
    {
        if ($this->emailNotificationIdUser === null) {
            return null;
        }

        $emailNotification = Eventsky::$plugin->emailNotification->getEmailNotificationById($this->emailNotificationIdUser);

        if (!$emailNotification) {
            throw new InvalidConfigException('Invalid email notification ID: ' . $this->emailNotificationIdUser);
        }

        return $emailNotification;
    }
}
