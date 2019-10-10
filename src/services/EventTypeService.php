<?php

namespace fredmansky\eventsky\services;

use craft\base\Component;
use craft\db\ActiveRecord;
use craft\db\Query;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use craft\models\Section;
use craft\models\Site;
use craft\records\FieldLayout;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\events\EventTypeEvent;
use fredmansky\eventsky\models\EventType;
use fredmansky\eventsky\models\EventTypeSite;
use fredmansky\eventsky\records\EventTypeRecord;
use fredmansky\eventsky\records\EventTypeSiteRecord;
use yii\db\ActiveQuery;

class EventTypeService extends Component
{
    public const EVENT_BEFORE_SAVE_EVENT_TYPE = 'beforeSaveEventType';
    
    /** @var array */
    private $eventTypes;

    public function init()
    {
        parent::init();
    }
    
    public function getAllEventTypes(): array
    {
        if ($this->eventTypes !== null) {
            return $this->eventTypes;
        }

        $results = $this->createEventTypeQuery()
            ->all();

        $this->eventTypes = array_map(function($result) {
            return new EventType($result);
        }, $results);
        return $this->eventTypes;
    }

    public function getEventTypeById(int $id): ?EventType
    {
        $result = $this->createEventTypeQuery()
            ->where(['=', 'id', $id])
            ->with(['fieldLayout'])
            ->one();

        if (!$result) return null;
        return new EventType($result);
    }

    /**
     * @param int $eventTypeId
     * @return EventTypeSite[]
     */
    public function getEventTypeSites(int $eventTypeId): array
    {
        $eventTypeSites = EventTypeSiteRecord::find()
            ->where(['=', 'eventtypeId', $eventTypeId])
            ->all();

        return array_map(function($eventType) {
            return new EventTypeSite($eventType);
        }, $eventTypeSites);
    }

    public function saveEventType(EventType $eventType, bool $runValidation = true)
    {
        $isNewEventType = !$eventType->id;
        
        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_EVENT_TYPE)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_EVENT_TYPE, new EventTypeEvent([
                'eventType' => $eventType,
                'isNew' => $isNewEventType
            ]));
        }
        
        if ($runValidation && !$eventType->validate()) {
            \Craft::info('Event Type not daved due to validation error.', __METHOD__);
            return false;
        }
        
        if ($isNewEventType) {
            $eventType->uid = StringHelper::UUID();
        } else if (!$eventType->uid) {
            $eventType->uid = Db::uidById(Table::EVENT_TYPES, $eventType->id);
        }

        $eventTypeRecord = EventTypeRecord::find()
            ->where(['=', 'id', $eventType->id])
            ->one();

        if (!$eventTypeRecord) {
            $eventTypeRecord = new EventTypeRecord();
        }

        $fieldLayout = $eventType->getFieldLayout();
        \Craft::$app->getFields()->saveLayout($fieldLayout);

        $eventTypeRecord->fieldLayoutId = (int) $fieldLayout->id;
        $eventTypeRecord->name = $eventType->name;
        $eventTypeRecord->handle = $eventType->handle;
        $eventTypeRecord->fieldLayoutId = $eventType->fieldLayoutId;
        $eventTypeRecord->uid = $eventType->uid;
        $eventTypeRecord->isWaitingListEnabled = $eventType->isWaitingListEnabled;
        $eventTypeRecord->isRegistrationEnabled = $eventType->isRegistrationEnabled;
        $eventTypeRecord->setFieldLayout($fieldLayout);
        $eventTypeRecord->save();

        $eventTypeId = $eventTypeRecord->id;#

        $allSiteIds = \Craft::$app->sites->getAllSiteIds();
        $eventTypeSites = $eventType->getEventTypeSites();
        foreach ($allSiteIds as $siteId) {
            $found = false;
            foreach ($eventTypeSites as $eventTypeSite) {
                if ($eventTypeSite->siteId === $siteId) $found = true;
            }
            if (!$found) {
                EventTypeSiteRecord::deleteAll(['siteId' => $siteId, 'eventtypeId' => $eventTypeId]);
            }
        }


        foreach($eventTypeSites as $eventTypeSite) {
            $eventTypeSiteRecord = EventTypeSiteRecord::find()
                ->where(['=', 'eventtypeId', $eventTypeId])
                ->andWhere(['=', 'siteId', $eventTypeSite->siteId])
                ->one();



            if (!$eventTypeSiteRecord) {
                $eventTypeSiteRecord = new EventTypeSiteRecord();
            }

            $eventTypeSiteRecord->eventtypeId = $eventTypeId;
            $eventTypeSiteRecord->siteId = $eventTypeSite->siteId;
            $eventTypeSiteRecord->hasUrls = $eventTypeSite->hasUrls;
            $eventTypeSiteRecord->uriFormat = $eventTypeSite->uriFormat;
            $eventTypeSiteRecord->enabledByDefault = $eventTypeSite->enabledByDefault;
            $eventTypeSiteRecord->template = $eventTypeSite->template;
            $eventTypeSiteRecord->save();
        }

        // @TODO add exceptions when saving is failing
        return true;
    }

    private function createEventTypeQuery(): ActiveQuery
    {
        return EventTypeRecord::find()
            ->orderBy(['name' => SORT_ASC]);
    }
}
