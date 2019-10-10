<?php

namespace fredmansky\eventsky\records;

use craft\db\ActiveRecord;
use craft\records\FieldLayout;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\models\EventTypeSite;
use yii\db\ActiveQueryInterface;

class EventTypeRecord extends ActiveRecord
{
    public static function tableName()
    {
        return Table::EVENT_TYPES;
    }

    public function setFieldLayout(\craft\models\FieldLayout $fieldLayout)
    {
        $this->fieldLayoutId = $fieldLayout->id;
    }

    public function getFieldLayout(): ActiveQueryInterface
    {
        return $this->hasOne(FieldLayout::class, ['id' => 'fieldLayoutId']);
    }

    public function getEventTypeSites(): ActiveQueryInterface
    {
        return $this->hasMany(EventTypeSiteRecord::class, ['eventtypeId' => 'id']);
    }
}