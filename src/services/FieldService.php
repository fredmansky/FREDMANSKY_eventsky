<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\helpers\ArrayHelper;
use craft\services\Fields;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;

class FieldService extends Fields
{
    public function getFieldByHandle(string $handle)
    {
        return ArrayHelper::firstWhere($this->getAllFields('fredmansky\eventsky\eventTicketTypeMappingField'), 'handle', $handle, true);
    }
}
