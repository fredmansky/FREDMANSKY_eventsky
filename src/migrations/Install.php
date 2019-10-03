<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\migrations;

use craft\db\Migration;

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

        if (!$this->db->tableExists('{{%eventsky_tickets}}')) {
            $this->createTable('{{%eventsky_tickets}}', [
                'id' => $this->primaryKey(),
            ]);
        }

        if (!$this->db->tableExists('{{%eventsky_eventtypes}}')) {
            $this->createTable('{{%eventsky_eventtypes}}', [
                'id' => $this->primaryKey(),
                'name' => $this->string(255),
                'handle' => $this->string(255),
                'fieldLayoutId' => $this->integer()->notNull(),
                'isRegistrationEnabled' => $this->boolean(),
                'isWaitingListEnabled' => $this->boolean(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);
        }

        if (!$this->db->tableExists('{{%eventsky_tickettypes}}')) {
            $this->createTable('{{%eventsky_tickettypes}}', [
                'id' => $this->primaryKey(),
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
        $this->addForeignKey($this->db->getForeignKeyName('{{%%eventsky_events}}',              'id'),                  '{{%%eventsky_events}}',            'id',               '{{%elements}}',        'id',   'CASCADE', null);
        $this->addForeignKey($this->db->getForeignKeyName('{{%%eventsky_eventtypes}}',          'fieldLayoutId'),       '{{%%eventsky_eventtypes}}',        'fieldLayoutId',    '{{%fieldlayouts}}',    'id',   'CASCADE', null);
    }

    protected function dropTables()
    {
        $this->dropTableIfExists('{{%eventsky_events}}');
        $this->dropTableIfExists('{{%eventsky_tickets}}');
        $this->dropTableIfExists('{{%eventsky_eventtypes}}');
        $this->dropTableIfExists('{{%eventsky_tickettypes}}');
        $this->dropTableIfExists('{{%eventsky_events_tickettypes}}');
    }
}