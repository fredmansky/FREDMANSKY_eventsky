<?php

namespace fredmansky\eventsky\records;

use craft\db\ActiveRecord;
use fredmansky\eventsky\db\Table;

class EventRecord extends ActiveRecord
{
    public static function tableName()
    {
        return Table::EVENTS;
    }

    public function getElement(): ActiveQueryInterface
    {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }

    public function getType(): ActiveQueryInterface
    {
        return $this->hasOne(EventTypeRecord::class, ['id' => 'eventtypeId']);
    }
}