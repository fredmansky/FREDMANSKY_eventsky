<?php

namespace fredmansky\eventsky\services;

use craft\base\Component;
use craft\db\ActiveRecord;
use fredmansky\eventsky\records\EventTypeRecord;

class EventTypeService extends Component
{
    public function init()
    {
        parent::init();
    }
    
    public function all()
    {
        return EventTypeRecord::find()->all();
    }

    public function byId(int $id): ?ActiveRecord
    {
        return EventTypeRecord::find()
            ->where(['=', 'id', $id])
            ->with(['fieldLayout'])
            ->one();
    }
}
