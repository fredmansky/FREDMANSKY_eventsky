<?php

namespace fredmansky\eventsky\records;

use craft\db\ActiveRecord;
use craft\records\FieldLayout;
use craft\records\Site;
use yii\db\ActiveQueryInterface;

class EventTypeSiteRecord extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%eventsky_eventtypes_sites}}';
    }

    public function getSite(): ActiveQueryInterface
    {
        return $this->hasOne(Site::class, ['id' => 'siteId']);
    }

    public function getEventType(): ActiveQueryInterface
    {
        return $this->hasOne(EventTypeRecord::class, ['id' => 'eventtypeId']);
    }
}