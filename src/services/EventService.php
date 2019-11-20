<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\EventTicketTypeMapping;
use fredmansky\eventsky\records\EventTicketTypeMappingRecord;
use yii\db\ActiveQuery;

class EventService extends Component
{
    /** @var array */
    private $events;

    public function init()
    {
        parent::init();
    }

    public function getAllEvents(): array
    {
        if ($this->events !== null) {
            return $this->events;
        }

        $this->events = Event::find()
        ->anyStatus()
        ->all();

        return $this->events;
    }

    public function getEventById(int $id): ?ElementInterface
    {
        if (!$id) {
            return null;
        }

        return Craft::$app->getElements()->getElementById($id, Event::class);
    }

    public function deleteEventById(int $id): bool
    {
        $event = $this->getEventById($id);

        if (!$event) {
            return false;
        }

        return $this->deleteEvent($event);
    }

    public function deleteEvent(Event $event): bool
    {
        $elementsService = Craft::$app->getElements();
        $elementsService->deleteElement($event);

        // Clear caches
        $this->events = null;

        return true;
    }

    public function getAllTicketTypeMappingsByEventId(int $eventId): array
    {
        $results = $this->createEventTicketTypeMappingQuery()
            ->where(['=', 'eventId', $eventId])
            ->all();

        return array_map(function($result) {
            return new EventTicketTypeMapping($result);
        }, $results);
    }

    public function getTicketTypeMapping(int $eventId, int $ticketTypeId): ?EventTicketTypeMapping
    {
        $result = $this->createEventTicketTypeMappingQuery()
            ->where(['=', 'eventId', $eventId])
            ->andWhere(['=', 'tickettypeId', $ticketTypeId])
            ->one();

        if($result) {
            return new EventTicketTypeMapping($result);
        }

        return null;
    }

    public function saveEventTicketTypeMapping(EventTicketTypeMapping $eventTicketTypeMapping)
    {
        $isNewMapping = !$eventTicketTypeMapping->id;

        if ($isNewMapping) {
            $eventTicketTypeMapping->uid = StringHelper::UUID();
        } else if (!$eventTicketTypeMapping->uid) {
            $eventTicketTypeMapping->uid = Db::uidById(Table::EVENT_TICKET_TYPES, $eventTicketTypeMapping->id);
        }

        $eventTicketTypeMappingRecord = EventTicketTypeMappingRecord::find()
            ->where(['=', 'id', $eventTicketTypeMapping->id])
            ->one();

        if (!$eventTicketTypeMappingRecord) {
            $eventTicketTypeMappingRecord = new EventTicketTypeMappingRecord();
        }

        $eventTicketTypeMappingRecord->tickettypeId = $eventTicketTypeMapping->tickettypeId;
        $eventTicketTypeMappingRecord->eventId = $eventTicketTypeMapping->eventId;
        $eventTicketTypeMappingRecord->limit = $eventTicketTypeMapping->limit;
        $eventTicketTypeMappingRecord->registrationStartDate = $eventTicketTypeMapping->registrationStartDate;
        $eventTicketTypeMappingRecord->registrationEndDate = $eventTicketTypeMapping->registrationEndDate;
        $eventTicketTypeMappingRecord->uid = $eventTicketTypeMapping->uid;

        $eventTicketTypeMappingRecord->save();

        // @TODO add exceptions when saving is failing
        return true;
    }

    public function deleteEventTicketTypeMapping(EventTicketTypeMapping $eventTicketTypeMapping)
    {
        $eventTicketTypeMappingRecord = EventTicketTypeMappingRecord::find()
            ->where(['=', 'id', $eventTicketTypeMapping->id])
            ->one();

        if (!$eventTicketTypeMappingRecord) {
            return false;
        }

        return $eventTicketTypeMappingRecord->delete();
    }

    private function createEventTicketTypeMappingQuery(): ActiveQuery
    {
        return EventTicketTypeMappingRecord::find();
    }
}
