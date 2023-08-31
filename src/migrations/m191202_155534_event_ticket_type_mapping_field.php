<?php

namespace fredmansky\eventsky\migrations;

use Craft;
use craft\db\Migration;
use craft\records\Field;
use fredmansky\eventsky\fields\EventTicketTypeMappingField;

class m191202_155534_event_ticket_type_mapping_field extends Migration
{
    public function safeUp()
    {
        $field = Craft::$app->fields->createField([
            'name' => EventTicketTypeMappingField::displayName(),
            'handle' => EventTicketTypeMappingField::FIELD_HANDLE,
            'context' => EventTicketTypeMappingField::FIELD_CONTEXT,
            'translationMethod' => 'none',
            'type' => EventTicketTypeMappingField::class,
            'settings' => ['minBlocks' => null, 'maxBlocks' => null],
        ]);

        Craft::$app->fields->saveField($field);
    }

    public function safeDown()
    {
        Field::deleteAll(['type' => EventTicketTypeMappingField::class]);
        return true;
    }
}
