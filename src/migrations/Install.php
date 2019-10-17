<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\migrations;

use craft\db\Migration;
use fredmansky\eventsky\db\Table;

class Install extends Migration
{

    public function safeUp() : bool
    {
        $this->createTables();
        return true;
    }


    public function safeDown() : bool
    {
        $this->dropTables();
        return true;
    }

    protected function createTables()
    {
        if (!$this->db->tableExists('{{%eventsky_events}}')) {
            $this->createTable('{{%eventsky_events}}', [
                'id' => $this->primaryKey(),
                'typeId' => $this->integer()->notNull(),
                'authorId' => $this->integer()->notNull(),
                'description' => $this->text(),
                'image' => $this->integer(),
                'startDate' => $this->dateTime()->notNull(),
                'endDate' => $this->dateTime(),
                'postDate' => $this->dateTime()->notNull(),
                'expiryDate' => $this->dateTime(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'needsRegistration' => $this->boolean(),
                'registrationEnabled' => $this->boolean(),
                'numberOfTickets' => $this->integer(),
                'waitingList' => $this->boolean(),
                'waitingListSize' => $this->integer(),
                'uid' => $this->uid(),
            ]);
        }

        if (!$this->db->tableExists(Table::TICKETS)) {
            $this->createTable(Table::TICKETS,
                [
                  'id' => $this->primaryKey(),
                  'name' => $this->string(255),
                  'handle' => $this->string(255),
                  'typeId' => $this->integer()->notNull(),
                  'description' => $this->text(),
                  'startDate' => $this->dateTime()->notNull(),
                  'endDate' => $this->dateTime(),
                  'postDate' => $this->dateTime()->notNull(),
                  'expiryDate' => $this->dateTime(),
                  'dateCreated' => $this->dateTime()->notNull(),
                  'dateUpdated' => $this->dateTime()->notNull(),
                  'dateDeleted' => $this->dateTime()->null(),
//                  'eventId' => $this->integer()->notNull(),
                  'uid' => $this->uid(),
                ]
            );
        }

        if (!$this->db->tableExists(Table::EVENT_TYPES)) {
            $this->createTable(Table::EVENT_TYPES, [
                'id' => $this->primaryKey(),
                'name' => $this->string(255),
                'handle' => $this->string(255),
                'fieldLayoutId' => $this->integer()->notNull(),
                'isRegistrationEnabled' => $this->boolean(),
                'isWaitingListEnabled' => $this->boolean(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'dateDeleted' => $this->dateTime(),
                'uid' => $this->uid(),
            ]);
        }

        if (!$this->db->tableExists('{{%eventsky_eventtypes_sites}}')) {
            $this->createTable('{{%eventsky_eventtypes_sites}}', [
                'id' => $this->primaryKey(),
                'eventtypeId' => $this->integer()->notNull(),
                'siteId' => $this->integer()->notNull(),
                'hasUrls' => $this->boolean(),
                'uriFormat' => $this->string(255),
                'template' => $this->string(255),
                'enabledByDefault' => $this->boolean(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }

        if (!$this->db->tableExists(Table::TICKET_TYPES)) {
            $this->createTable(Table::TICKET_TYPES, [
                'id' => $this->primaryKey(),
                'name' => $this->string(255),
                'handle' => $this->string(255),
                'fieldLayoutId' => $this->integer()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'dateDeleted' => $this->dateTime(),
                'uid' => $this->uid(),
            ]);
        }

        if (!$this->db->tableExists('{{%eventsky_events_tickettypes}}')) {
            $this->createTable('{{%eventsky_events_tickettypes}}', [
                'id' => $this->primaryKey(),
            ]);
        }
    }

    protected function createForeignKeys()
    {
        $this->addForeignKey($this->db->getForeignKeyName('{{%%eventsky_events}}', 'id'), '{{%%eventsky_events}}', 'id', '{{%elements}}', 'id', 'CASCADE', null);
        $this->addForeignKey($this->db->getForeignKeyName('{{%%eventsky_eventtypes}}', 'fieldLayoutId'), '{{%%eventsky_eventtypes}}', 'fieldLayoutId', '{{%fieldlayouts}}', 'id', 'CASCADE', null);
        $this->addForeignKey($this->db->getForeignKeyName('{{%%eventsky_eventtypes_sites}}', 'siteId'), '{{%%eventsky_eventtypes_sites}}', 'siteId', '{{%sites}}', 'id', 'CASCADE', null);
        $this->addForeignKey($this->db->getForeignKeyName('{{%%eventsky_eventtypes_sites}}', 'eventtypeId'), '{{%%eventsky_eventtypes_sites}}', 'eventtypeId', Table::EVENT_TYPES, 'id', 'CASCADE', null);
        $this->addForeignKey($this->db->getForeignKeyName('{{%%eventsky_eventtypes_sites}}', 'eventtypeId'), '{{%%eventsky_eventtypes_sites}}', 'eventtypeId', '{{%eventsky_eventtypes}}', 'id', 'CASCADE', null);
        $this->addForeignKey($this->db->getForeignKeyName('{{%%eventsky_tickets}}', 'id'), '{{%%eventsky_tickets}}', 'id', '{{%elements}}', 'id', 'CASCADE', null);
        $this->addForeignKey($this->db->getForeignKeyName('{{%%eventsky_tickettypes}}', 'fieldLayoutId'), '{{%%eventsky_tickettypes}}', 'fieldLayoutId', '{{%fieldlayouts}}', 'id', 'CASCADE', null);
    }

    protected function dropTables()
    {
        $this->dropTableIfExists('{{%eventsky_events}}');
        $this->dropTableIfExists('{{%eventsky_tickets}}');
        $this->dropTableIfExists(Table::EVENT_TYPES);
        $this->dropTableIfExists(Table::TICKET_TYPES);
        $this->dropTableIfExists('{{%eventsky_events_tickettypes}}');
        $this->dropTableIfExists('{{%eventsky_tickets}}');
        $this->dropTableIfExists('{{%eventsky_tickettypes}}');
    }
}