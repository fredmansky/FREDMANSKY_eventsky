<?php

namespace fredmansky\eventsky\records;

use craft\db\ActiveRecord;
use fredmansky\eventsky\db\Table;

class EmailNotificationRecord extends ActiveRecord
{
    public static function tableName()
    {
      return Table::EMAIL_NOTIFICATIONS;
    }
}