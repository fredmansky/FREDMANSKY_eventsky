<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\migrations;

use craft\db\Migration;
use craft\helpers\MigrationHelper;

class Install extends Migration
{
    /**
     * @return bool
     */
    public function safeUp() : bool
    {
        $this->createTables();
        //$this->createForeignKeys();
        return true;
    }

    /**
     * @return bool
     */
    public function safeDown() : bool
    {
        $this->dropTables();
        // $this->dropForeignKeys();
        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return void
     */
    protected function createTables()
    {
        if (!$this->db->tableExists('{{%eventsky_events}}')) {
            $this->createTable(
                '{{%eventsky_events}}',
                [
                    'id' => $this->primaryKey(),
                    'ticketTypeId' => $this->integer()->notNull(),
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
                ]
            );
        }

        if (!$this->db->tableExists('{{%eventsky_tickets}}')) {
            $this->createTable(
                '{{%eventsky_tickets}}',
                [
                    'id' => $this->primaryKey(),
                    'ticketTypeId' => $this->integer()->notNull(),
                    'eventId' => $this->integer()->notNull(),
                    'authorId' => $this->integer(),
                    'description' => $this->string(),
                    'startDate' => $this->dateTime()->notNull(),
                    'endDate' => $this->dateTime(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                ]
            );
        }

        if (!$this->db->tableExists('{{%eventsky_eventtypes}}')) {
            $this->createTable(
                '{{%eventsky_eventtypes}}',
                [
                    'id' => $this->primaryKey(),
                ]
            );
        }

        if (!$this->db->tableExists('{{%eventsky_tickettypes}}')) {
            $this->createTable(
                '{{%eventsky_tickettypes}}',
                [
                    'id' => $this->primaryKey(),
                ]
            );
        }

        if (!$this->db->tableExists('{{%eventsky_events_tickettypes}}')) {
            $this->createTable(
                '{{%eventsky_events_tickettypes}}',
                [
                    'id' => $this->primaryKey(),
                    'name' => $this->text(),
                    'description' => $this->text(),
                ]
            );
        }
    }

    /**
     * @return void
     */
    protected function createForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%eventsky_events}}', 'id'),
      '{{%eventsky_events}}', 'id', '{{%elements}}', 'id', 'CASCADE', null);

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%eventsky_tickets}}', 'eventId'),
      '{{%eventsky_tickets}}', 'eventId', '{{%eventsky_events}}', 'id', 'CASCADE', null);

        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%eventsky_tickets}}', 'ticketTypeId'),
      '{{%eventsky_tickets}}', 'ticketTypeId', '{{%eventsky_tickettypes}}', 'id', 'CASCADE', null);

    }

    protected function dropForeignKeys()
    {
      MigrationHelper::dropAllForeignKeysOnTable('{{%eventsky_events}}', $this);
      MigrationHelper::dropAllForeignKeysOnTable('{{%eventsky_tickets}}', $this);
      MigrationHelper::dropAllForeignKeysOnTable('{{%eventsky_eventtypes}}', $this);
      MigrationHelper::dropAllForeignKeysOnTable('{{%eventsky_tickettypes}}', $this);
      MigrationHelper::dropAllForeignKeysOnTable('{{%eventsky_events_tickettypes}}', $this);
    }

    /**
     * @return void
     */
    protected function dropTables()
    {
        $this->dropTableIfExists('{{%eventsky_events}}');
        $this->dropTableIfExists('{{%eventsky_tickets}}');
        $this->dropTableIfExists('{{%eventsky_eventtypes}}');
        $this->dropTableIfExists('{{%eventsky_tickettypes}}');
        $this->dropTableIfExists('{{%eventsky_events_tickettypes}}');
    }
}