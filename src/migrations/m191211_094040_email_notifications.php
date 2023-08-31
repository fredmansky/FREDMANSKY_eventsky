<?php

namespace fredmansky\eventsky\migrations;

use craft\db\Migration;
use fredmansky\eventsky\db\Table;

/**
 * m191211_094040_email_notifications migration.
 */
class m191211_094040_email_notifications extends Migration
{
    public function safeUp()
    {
        $this->addEmailNotificationTable();
        $this->addEmailNotificationFields();
        $this->addForeignKeyRelations();
    }

    public function safeDown()
    {
        $this->removeEmailNotificationFieldTicketType();
        $this->dropEmailNotificationTable();
    }

    private function addEmailNotificationTable() {
        if (!$this->db->tableExists(Table::EMAIL_NOTIFICATIONS)) {
            $this->createTable(Table::EMAIL_NOTIFICATIONS, [
                'id' => $this->primaryKey(),
                'name' => $this->string()->notNull(),
                'handle' => $this->string()->notNull(),
                'subject' => $this->string()->null(),
                'fromEmail' => $this->string()->notNull(),
                'replyToEmail' => $this->string()->null(),
                'textContent' => $this->text()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }
    }

    private function addEmailNotificationFields() {
        $this->addColumn(Table::TICKET_TYPES, 'emailNotificationIdUser', 'INT');
        $this->addColumn(Table::TICKETS, 'email', 'VARCHAR(255) NOT NULL');

        $this->addColumn(Table::EVENT_TYPES, 'emailNotificationIdAdmin', 'INT');
        $this->addColumn(Table::EVENT_TYPES, 'emailNotificationAdminEmails', 'VARCHAR(255)');

        $this->addColumn(Table::EVENTS, 'emailNotificationIdAdmin', 'INT');
        $this->addColumn(Table::EVENTS, 'emailNotificationAdminEmails', 'VARCHAR(255)');
    }

    private function addForeignKeyRelations() {
        $this->addForeignKey(null, Table::TICKET_TYPES, ['emailNotificationIdUser'], Table::EMAIL_NOTIFICATIONS, ['id'], 'SET NULL', null);
        $this->addForeignKey(null, Table::EVENT_TYPES, ['emailNotificationIdAdmin'], Table::EMAIL_NOTIFICATIONS, ['id'], 'SET NULL', null);
        $this->addForeignKey(null, Table::EVENTS, ['emailNotificationIdAdmin'], Table::EMAIL_NOTIFICATIONS, ['id'], 'SET NULL', null);
    }

    private function dropEmailNotificationTable() {
        $this->dropTableIfExists(Table::EMAIL_NOTIFICATIONS);
    }

    private function removeEmailNotificationFieldTicketType() {
        $this->dropColumn(Table::TICKET_TYPES, 'emailNotificationIdUser');
        $this->dropColumn(Table::TICKETS, 'email');
        $this->dropColumn(Table::EVENT_TYPES, 'emailNotificationIdAdmin');
        $this->dropColumn(Table::EVENT_TYPES, 'emailNotificationAdminEmails');
        $this->dropColumn(Table::EVENTS, 'emailNotificationIdAdmin');
        $this->dropColumn(Table::EVENTS, 'emailNotificationAdminEmails');
    }
}
