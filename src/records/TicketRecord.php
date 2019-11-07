<?php

namespace fredmansky\eventsky\records;

use craft\base\Element;
use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;
use fredmansky\eventsky\db\Table;
use yii\db\ActiveQueryInterface;

class TicketRecord extends ActiveRecord
{
    public static function tableName()
    {
        return Table::TICKETS;
    }

    public function getElement(): ActiveQueryInterface
    {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }

    public function getType(): ActiveQueryInterface
    {
        return $this->hasOne(TicketTypeRecord::class, ['id' => 'typeId']);
    }

    public function getEvent(): ActiveQueryInterface
    {
        return $this->hasOne(EventRecord::class, ['id' => 'eventId']);
    }

    public function getStatus(): ActiveQueryInterface
    {
        return $this->hasOne(TicketStatusRecord::class, ['id' => 'statusId']);
    }
}