<?php

namespace fredmansky\eventsky\services;

use craft\base\Component;
use craft\db\ActiveRecord;
use craft\db\Query;
use fredmansky\eventsky\models\EventType;
use fredmansky\eventsky\models\EventTypeSite;
use fredmansky\eventsky\records\EventTypeRecord;
use fredmansky\eventsky\records\EventTypeSiteRecord;
use yii\db\ActiveQuery;

class EventTypeService extends Component
{
    /** @var array */
    private $eventTypes;

    public function init()
    {
        parent::init();
    }
    
    public function getAllEventTypes(): array
    {
        if ($this->eventTypes !== null) {
            return $this->$eventTypes;
        }

        $results = $this->createEventTypeQuery()
            ->all();

        $this->eventTypes = array_map(function($result) {
            return new EventType($result);
        }, $results);
        return $this->eventTypes;
    }

    public function getEventTypeById(int $id): ?ActiveRecord
    {
        return $this->createEventTypeQuery()
            ->where(['=', 'id', $id])
            ->with(['fieldLayout'])
            ->one();
    }

    public function getEventTypeSites(int $eventTypeId): array
    {
        $eventTypeSites = EventTypeSiteRecord::find()
            ->where(['=', 'eventtypeId', $eventTypeId])
            ->all();

        return array_map(function($eventType) {
            return new EventTypeSite($eventType);
        }, $eventTypeSites);
    }

    private function createEventTypeQuery(): ActiveQuery
    {
        return EventTypeRecord::find()
            ->orderBy(['name' => SORT_ASC]);
    }
}
