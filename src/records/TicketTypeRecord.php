<?php

namespace fredmansky\eventsky\records;

use craft\db\ActiveRecord;
use craft\db\SoftDeleteTrait;
use craft\records\FieldLayout;
use fredmansky\eventsky\db\Table;
use yii\db\ActiveQueryInterface;

class TicketTypeRecord extends ActiveRecord
{
    use SoftDeleteTrait;

    public static function tableName()
    {
      return Table::TICKET_TYPES;
    }

    public function setFieldLayout(\craft\models\FieldLayout $fieldLayout)
    {
      $this->fieldLayoutId = $fieldLayout->id;
    }

    public function getFieldLayout(): ActiveQueryInterface
    {
        return $this->hasOne(FieldLayout::class, ['id' => 'fieldLayoutId']);
    }
}