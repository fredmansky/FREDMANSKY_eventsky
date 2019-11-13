<?php

namespace fredmansky\eventsky\records;

use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;
use fredmansky\eventsky\db\Table;

class TicketStatusRecord extends ActiveRecord
{
    use SoftDeleteTrait;

    public static function tableName()
    {
        return Table::TICKET_STATUSES;
    }
}