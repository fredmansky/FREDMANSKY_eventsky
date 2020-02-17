<?php

namespace fredmansky\eventsky\migrations;

use Craft;
use craft\db\Migration;
use fredmansky\eventsky\db\Table;

/**
 * m200217_132831_postdate_nullable migration.
 */
class m200217_132831_postdate_nullable extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn(Table::EVENTS, 'postDate', $this->dateTime()->null());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
    }
}
