<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\migrations;

use Craft;
use craft\db\Migration;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\errors\TableAlreadyExistsException;

/**
 * Eventsky Install Migration
 *
 * If your plugin needs to create any custom database tables when it gets installed,
 * create a migrations/ folder within your plugin folder, and save an Install.php file
 * within it using the following template:
 *
 * If you need to perform any additional actions on install/uninstall, override the
 * safeUp() and safeDown() methods.
 *
 * @author    Fredmansky
 * @package   Eventsky
 * @since     2.0.0
 */
class Install extends Migration
{
    // Public Properties
    // =========================================================================

    /**
     * @var string The database driver to use
     */
    public $driver;

    // Public Methods
    // =========================================================================

    /**
     * This method contains the logic to be executed when applying this migration.
     * This method differs from [[up()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[up()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeUp(): bool
    {
        $this->createTables();
        $this->addForeignKeys();
        $this->refreshDBSchemaCaches();
        $this->insertDefaultData();

        return true;
    }

    /**
     * This method contains the logic to be executed when removing this migration.
     * This method differs from [[down()]] in that the DB logic implemented here will
     * be enclosed within a DB transaction.
     * Child classes may implement this method instead of [[down()]] if the DB logic
     * needs to be within a transaction.
     *
     * @return boolean return a false value to indicate the migration fails
     * and should not proceed further. All other return values mean the migration succeeds.
     */
    public function safeDown(): bool
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables(): bool
    {
        $this->createTableForEvents();

        return true;
    }

    /**
     * Creates the indexes needed for the Records used by the plugin
     *
     * @return void
     */
    protected function createIndexes()
    {
    }

    /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys()
    {
    }

    protected function refreshDBSchemaCaches()
    {
        Craft::$app->db->schema->refresh();
    }

    /**
     * Populates the DB with the default data.
     *
     * @return void
     */
    protected function insertDefaultData()
    {
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables()
    {
        $tables = [
            Table::EVENTS,
        ];

        /** @var $table string */
        foreach ($tables as $table) {
            $this->dropTableIfExists($table);
        }
    }

    // Private Methods
    // =========================================================================

    /**
     * @throws TableAlreadyExistsException
     */
    private function createTableForEvents()
    {
        $tableSchema = $this->db->schema->getTableSchema(Table::EVENTS);
        $tableExists = $this->db->tableExists(Table::EVENTS);

        if ($tableSchema || $tableExists) {
            throw new TableAlreadyExistsException('Table ' . Table::EVENTS . ' already exists.');
        }

        $this->createTable(Table::EVENTS, [
            'id' => $this->primaryKey(),
            'startDate' => $this->dateTime()->notNull(),
            'endDate' => $this->dateTime()->null(),
            'postDate' => $this->dateTime()->notNull(),
            'expiryDate' => $this->dateTime()->null(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }
}
