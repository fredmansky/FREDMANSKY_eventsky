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

    public function init(): void
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

    public function getAllEventIds(): array
    {
        $allEvents = $this->getAllEvents();
        return array_map(function($event) {
            return $event->id;
        }, $allEvents);
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

    public function getAllTicketTypeMappingsByEventId(int $eventId = null): array
    {
        $results = $this->createEventTicketTypeMappingQuery()
            ->where(['=', 'eventId', $eventId])
            ->all();

        return array_map(function($result) {
            return $this->createEventTicketTypeMappingFromRecord($result);
        }, $results);
    }

    public function getTicketTypeMapping(int $eventId = null, int $ticketTypeId = null): ?EventTicketTypeMapping
    {
        $result = $this->createEventTicketTypeMappingQuery()
            ->where(['=', 'eventId', $eventId])
            ->andWhere(['=', 'tickettypeId', $ticketTypeId])
            ->one();

        if($result) {
            return $this->createEventTicketTypeMappingFromRecord($result);
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
        $eventTicketTypeMappingRecord->isFree = $eventTicketTypeMapping->isFree;
        $eventTicketTypeMappingRecord->price = $eventTicketTypeMapping->price;
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

    public function getEventHashBy($eventId) {
        return substr(hash('sha256', $eventId), 0, 10);
    }

    private function createEventTicketTypeMappingQuery(): ActiveQuery
    {
        return EventTicketTypeMappingRecord::find();
    }

    private function createEventTicketTypeMappingFromRecord(EventTicketTypeMappingRecord $eventTicketTypeMappingRecord): EventTicketTypeMapping
    {
        return new EventTicketTypeMapping($eventTicketTypeMappingRecord->getAttributes());
    }
}
