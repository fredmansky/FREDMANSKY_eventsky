<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\models;

use Craft;
use craft\base\Model;
use craft\behaviors\FieldLayoutBehavior;
use craft\helpers\UrlHelper;

/**
 * EmailNotification model class.
 *
 * @mixin FieldLayoutBehavior
 * @author Fredmansky
 * @since 3.0
 *
 * @property string $cpEditUrl
 */
class EmailNotification extends Model
{
    public $id;
    public $name;
    public $handle;
    public $subject;
    public $fromEmail;
    public $replyToEmail;
    public $textContent;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    public function attributeLabels(): array
    {
        return [
            'handle' => Craft::t('app', 'Handle'),
            'name' => Craft::t('app', 'Name'),
        ];
    }

    public function __toString(): string
    {
        return (string)$this->handle ?: static::class;
    }

    public function getCpEditUrl(): string
    {
        return UrlHelper::cpUrl('eventsky/emailnotifications/' . $this->id);
    }
}
