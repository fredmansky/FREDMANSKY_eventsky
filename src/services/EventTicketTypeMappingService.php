<?php

namespace fredmansky\eventsky\services;

use craft\base\Component;
use fredmansky\eventsky\models\EventTicketTypeMapping;
use fredmansky\eventsky\records\EventTicketTypeMappingRecord;
use yii\db\ActiveQuery;

class EventTicketTypeMappingService extends Component
{
    public function init()
    {
        parent::init();
    }

    public function getAllTicketTypesByEventId(int $eventId): array
    {
        $results = $this->createEventTicketTypeMappingQuery()
            ->where(['=', 'eventId', $eventId])
            ->all();

        return array_map(function($result) {
            return new EventTicketTypeMapping($result);
        }, $results);
    }

    private function createEventTicketTypeMappingQuery(): ActiveQuery
    {
        return EventTicketTypeMappingRecord::find();
    }
}
