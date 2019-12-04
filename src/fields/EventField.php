<?php

namespace fredmansky\eventsky\fields;

use Craft;
use craft\fields\BaseRelationField;
use fredmansky\eventsky\elements\Event;
use yii\helpers\StringHelper;

class EventField extends BaseRelationField
{
    public static function displayName(): string
    {
        return Craft::t('eventsky', 'translate.fields.event.displayName');
    }

    public static function defaultSelectionLabel(): string
    {
        return Craft::t('eventsky', 'translate.fields.event.addType');
    }

    public static function elementType(): string
    {
        return Event::class;
    }
}