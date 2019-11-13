<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\migrations;

use craft\db\Migration;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\records\TicketStatusRecord;

class Install extends Migration
{

    public function safeUp() : bool
    {
        $this->createTables();
        $this->addForeignKeys();
        $this->insertDefaultData();
        return true;
    }


    public function safeDown() : bool
    {
        $this->dropTables();
        return true;
    }

    public function insertDefaultData()
    {
        $this->defaultTicketStatuses();
    }

    protected function createTables()
    {
        // EVENTS
        if (!$this->db->tableExists(Table::EVENTS)) {
            $this->createTable(Table::EVENTS, [
                'id' => $this->primaryKey(),
                'typeId' => $this->integer()->notNull(),
                'startDate' => $this->dateTime()->notNull(),
                'endDate' => $this->dateTime()->null(),
                'postDate' => $this->dateTime()->notNull(),
                'expiryDate' => $this->dateTime()->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'needsRegistration' => $this->boolean()->null(),
                'registrationEnabled' => $this->boolean()->null(),
                'totalTickets' => $this->integer()->null(),
                'hasWaitingList' => $this->boolean()->null(),
                'waitingListSize' => $this->integer()->null(),
                'uid' => $this->uid(),
            ]);
        }

        // EVENT TICKET TYPES
        if (!$this->db->tableExists(Table::EVENT_TICKET_TYPES)) {
            $this->createTable(Table::EVENT_TICKET_TYPES, [
                'id' => $this->primaryKey(),
                'tickettypeId' => $this->integer()->notNull(),
                'eventId' => $this->integer()->notNull(),
                'limit' => $this->integer()->null(),
                'registrationStartDate' => $this->dateTime()->null(),
                'registrationEndDate' => $this->dateTime()->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }

        // EVENT TYPES
        if (!$this->db->tableExists(Table::EVENT_TYPES)) {
            $this->createTable(Table::EVENT_TYPES, [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull(),
                'handle' => $this->string(255)->notNull(),
                'fieldLayoutId' => $this->integer()->null(),
                'isRegistrationEnabled' => $this->boolean()->null(),
                'isWaitingListEnabled' => $this->boolean()->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'dateDeleted' => $this->dateTime()->null(),
                'uid' => $this->uid(),
            ]);
        }

        // EVENT TYPE SITES
        if (!$this->db->tableExists(Table::EVENT_TYPES_SITES)) {
            $this->createTable(Table::EVENT_TYPES_SITES, [
                'id' => $this->primaryKey(),
                'eventtypeId' => $this->integer()->notNull(),
                'siteId' => $this->integer()->notNull(),
                'hasUrls' => $this->boolean()->null(),
                'uriFormat' => $this->string(255)->null(),
                'template' => $this->string(255)->null(),
                'enabledByDefault' => $this->boolean()->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }

        // TICKETS
        if (!$this->db->tableExists(Table::TICKETS)) {
            $this->createTable(Table::TICKETS, [
                    'id' => $this->primaryKey(),
                    'typeId' => $this->integer()->notNull(),
                    'eventId' => $this->integer()->notNull(),
                    'statusId' => $this->integer()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                ]
            );
        }

        // TICKET STATUSES
        if (!$this->db->tableExists(Table::TICKET_STATUSES)) {
            $this->createTable(Table::TICKET_STATUSES, [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull(),
                'handle' => $this->string(255)->notNull(),
                'color' => $this->enum('color', ['green', 'orange', 'red', 'blue', 'yellow', 'pink', 'purple', 'turquoise', 'light', 'grey', 'black'])->notNull()->defaultValue('green'),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'dateDeleted' => $this->dateTime()->null(),
                'uid' => $this->uid(),
            ]);
        }

        // TICKET TYPES
        if (!$this->db->tableExists(Table::TICKET_TYPES)) {
            $this->createTable(Table::TICKET_TYPES, [
                'id' => $this->primaryKey(),
                'name' => $this->string(255)->notNull(),
                'handle' => $this->string(255)->notNull(),
                'fieldLayoutId' => $this->integer()->null(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'dateDeleted' => $this->dateTime()->null(),
                'uid' => $this->uid(),
            ]);
        }
    }

    protected function addForeignKeys()
    {
        // EVENTS
        $this->addForeignKey(null, Table::EVENTS, ['id'], Table::ELEMENTS, ['id'], 'CASCADE', null);
        $this->addForeignKey(null, Table::EVENTS, ['typeId'], Table::EVENT_TYPES, ['id'], 'CASCADE', null);

        // EVENT TICKET TYPES
        $this->addForeignKey(null, Table::EVENT_TICKET_TYPES, ['tickettypeId'], Table::TICKET_TYPES, ['id'], 'CASCADE', null);
        $this->addForeignKey(null, Table::EVENT_TICKET_TYPES, ['eventId'], Table::EVENTS, ['id'], 'CASCADE', null);

        // EVENT TYPES
        $this->addForeignKey(null, Table::EVENT_TYPES, ['fieldLayoutId'], Table::FIELDLAYOUTS, ['id'], 'SET NULL', null);

        // EVENT TYPE SITES
        $this->addForeignKey(null, Table::EVENT_TYPES_SITES, ['siteId'], Table::SITES, ['id'], 'CASCADE', null);
        $this->addForeignKey(null, Table::EVENT_TYPES_SITES, ['eventtypeId'], Table::EVENT_TYPES, ['id'], 'CASCADE', null);

        // TICKETS
        $this->addForeignKey(null, Table::TICKETS, ['id'], Table::ELEMENTS, ['id'], 'CASCADE', null);
        $this->addForeignKey(null, Table::TICKETS, ['typeId'], Table::EVENT_TYPES, ['id'], 'CASCADE', null);
        $this->addForeignKey(null, Table::TICKETS, ['eventId'], Table::EVENTS, ['id'], 'CASCADE', null);
        $this->addForeignKey(null, Table::TICKETS, ['statusId'], Table::TICKET_STATUSES, ['id'], 'CASCADE', null);

        // TICKET STATUSES

        // TICKET TYPES
        $this->addForeignKey(null, Table::TICKET_TYPES, ['fieldLayoutId'], Table::FIELDLAYOUTS, ['id'], 'SET NULL', null);
    }

    protected function dropTables()
    {
        $this->dropTableIfExists(Table::EVENTS);
        $this->dropTableIfExists(Table::EVENT_TICKET_TYPES);
        $this->dropTableIfExists(Table::EVENT_TYPES);
        $this->dropTableIfExists(Table::EVENT_TYPES_SITES);
        $this->dropTableIfExists(Table::TICKETS);
        $this->dropTableIfExists(Table::TICKET_STATUSES);
        $this->dropTableIfExists(Table::TICKET_TYPES);
    }

    private function defaultTicketStatuses()
    {
        $data = [
            'name' => 'Approved',
            'handle' => 'approved',
            'color' => 'green',
        ];
        $this->insert(TicketStatusRecord::tableName(), $data);

        $data = [
            'name' => 'Waitlisted',
            'handle' => 'waitlisted',
            'color' => 'yellow',
        ];
        $this->insert(TicketStatusRecord::tableName(), $data);

        $data = [
            'name' => 'Denied',
            'handle' => 'denied',
            'color' => 'red',
        ];
        $this->insert(TicketStatusRecord::tableName(), $data);
    }
}