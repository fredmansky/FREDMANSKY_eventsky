<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;

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
        ->typeId(Eventsky::$plugin->eventType->getAllEventTypes())
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
}
