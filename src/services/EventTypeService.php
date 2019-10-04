<?php

namespace fredmansky\eventsky\services;

use craft\base\Component;
use craft\db\ActiveRecord;
use craft\db\Query;
use fredmansky\eventsky\models\EventType;
use fredmansky\eventsky\records\EventTypeRecord;
use yii\db\ActiveQuery;

class EventTypeService extends Component
{
    private $eventTypes;

    public function init()
    {
        parent::init();
    }
    
    public function getAllEventTypes()
    {
        if ($this->eventTypes !== null) {
            return $this->$eventTypes;
        }

        $results = $this->createEventTypeQuery()
            ->all();

        $this->eventTypes = [];

        foreach ($results as $result) {
//            if (!empty($result['previewTargets'])) {
//                $result['previewTargets'] = Json::decode($result['previewTargets']);
//            } else {
//                $result['previewTargets'] = [];
//            }

            $this->eventTypes[] = new EventType($result);
        }

        return $this->eventTypes;
    }

    public function byId(int $id): ?ActiveRecord
    {
        return $this->createEventTypeQuery()
            ->where(['=', 'id', $id])
            ->with(['fieldLayout'])
            ->one();
    }

    private function createEventTypeQuery(): ActiveQuery
    {
        return EventTypeRecord::find()
            ->orderBy(['name' => SORT_ASC]);
    }
}
