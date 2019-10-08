<?php

namespace fredmansky\eventsky\records;

use craft\db\ActiveRecord;
use craft\records\FieldLayout;
use yii\db\ActiveQueryInterface;

class TicketTypeRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%eventsky_tickettypes}}';
    }

    public function getFieldLayout(): ActiveQueryInterface
    {
        return $this->hasOne(FieldLayout::class, ['id' => 'fieldLayoutId']);
    }
}