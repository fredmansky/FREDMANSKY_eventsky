<?php

namespace fredmansky\eventsky\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;
use yii\helpers\Html;

class EventTicketTypeMappingField extends Field
{
    const FIELD_HANDLE = 'availableTickets';

    public static function displayName(): string
    {
        return Craft::t('eventsky', 'translate.fields.eventTicketTypeMapping.displayName');
    }

    public $minBlocks;
    public $maxBlocks;

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    public static function hasContentColumn(): bool
    {
        return false;
    }

    public function rules()
    {
        $rules = [];
        return $rules;
    }

    public function normalizeValue($value, ElementInterface $element = null)
    {
//        if ($value instanceof ElementQueryInterface) {
//            return $value;
//        }

        $mappedTicketTypes = [];

        // Existing element?
        if ($element && $element->id) {
            $mappedTicketTypes = Eventsky::$plugin->eventTicketTypeMapping->getAllTicketTypesByEventId($element->id);
        }

        return $mappedTicketTypes;
    }

    public function serializeValue($value, ElementInterface $element = null)
    {
//        /** @var MatrixBlockQuery $value */
//        $serialized = [];
//        $new = 0;
//
//        foreach ($value->all() as $block) {
//            $blockId = $block->id ?? 'new' . ++$new;
//            $serialized[$blockId] = [
//                'type' => $block->getType()->handle,
//                'enabled' => $block->enabled,
//                'collapsed' => $block->collapsed,
//                'fields' => $block->getSerializedFieldValues(),
//            ];
//        }
//
//        return $serialized;
        echo 'GETTING HERE';
        die();
    }

    public function getInputHtml($value, ElementInterface $element = null): string
    {



//        dump($value);
//        die();
//        dump($element);
//        die();
        $data = [];
        $data['allTicketTypes'] = Eventsky::$plugin->ticketType->getAllTicketTypes();
        $data['mappedTicketTypes'] = $value;

        return Craft::$app->getView()->renderTemplate('eventsky/_components/fieldTypes/EventTicketTypeMapping/input', $data);
    }
}
