<?php

namespace fredmansky\eventsky\migrations;

use Craft;
use craft\db\Migration;
use fredmansky\eventsky\db\Table;

class m191203_164853_dynamic_titles extends Migration
{
    public function safeUp()
    {
        $this->addTitleFormatColumnToEventType();
        $this->addTitleFormatColumnToTicketType();
    }

    protected function addTitleFormatColumnToEventType () {
        if (!$this->db->columnExists(Table::EVENT_TYPES, 'titleFormat')) {
            $this->addColumn(Table::EVENT_TYPES, 'titleFormat', $this->string()->after('handle')->null());
        }
    }

    protected function addTitleFormatColumnToTicketType () {
        if (!$this->db->columnExists(Table::TICKET_TYPES, 'titleFormat')) {
            $this->addColumn(Table::TICKET_TYPES, 'titleFormat', $this->string()->after('handle')->null());
        }
    }

    public function safeDown()
    {
        $this->dropColumn(Table::EVENT_TYPES, 'titleFormat');
        $this->dropColumn(Table::TICKET_TYPES, 'titleFormat');
    }
}
