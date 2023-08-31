<?php

namespace fredmansky\eventsky\migrations;

use craft\db\Migration;
use fredmansky\eventsky\db\Table;

/**
 * m191215_004541_add_price_to_event_tickettype_mapping migration.
 */
class m191215_004541_add_price_to_event_tickettype_mapping extends Migration
{
    public function safeUp()
    {
        $this->addPriceColumnToEventTicketTypeMapping();
        $this->addIsFreeColumnToEventTicketTypeMapping();
    }

    protected function addPriceColumnToEventTicketTypeMapping () {
        if (!$this->db->columnExists(Table::EVENT_TICKET_TYPES, 'price')) {
            $this->addColumn(Table::EVENT_TICKET_TYPES, 'price', $this->string()->after('limit')->null());
        }
    }

    protected function addIsFreeColumnToEventTicketTypeMapping () {
        if (!$this->db->columnExists(Table::EVENT_TICKET_TYPES, 'isFree')) {
            $this->addColumn(Table::EVENT_TICKET_TYPES, 'isFree', $this->boolean()->after('limit'));
        }
    }

    public function safeDown()
    {
        $this->dropColumn(Table::EVENT_TICKET_TYPES, 'price');
        $this->dropColumn(Table::EVENT_TICKET_TYPES, 'isFree');
    }
}
