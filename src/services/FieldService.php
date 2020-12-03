<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\events\ConfigEvent;
use craft\helpers\ArrayHelper;
use craft\services\Fields;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\fields\EventTicketTypeMappingField;

class FieldService extends Fields
{
    /**
     * @param string $handle
     * @param null $context
     * @return \craft\base\FieldInterface|mixed|null
     */
    public function getFieldByHandle(string $handle, $context = null)
    {
        return ArrayHelper::firstWhere($this->getAllFields(EventTicketTypeMappingField::FIELD_CONTEXT), 'handle', $handle, true);
    }
}
