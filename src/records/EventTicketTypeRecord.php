<?php

namespace fredmansky\eventsky\records;

use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;
use craft\records\FieldLayout;
use craft\records\Site;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\models\TicketType;
use yii\db\ActiveQueryInterface;

class EventTicketTypeRecord extends ActiveRecord
{
    public static function tableName()
    {
      return Table::EVENT_TICKET_TYPES;
    }

    public function getEvent(): ActiveQueryInterface
    {
        return $this->hasOne(Event::class, ['id' => 'eventId']);
    }

    public function getTicketType(): ActiveQueryInterface
    {
        return $this->hasOne(TicketType::class, ['id' => 'tickettypeId']);
    }
}