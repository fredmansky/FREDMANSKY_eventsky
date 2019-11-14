<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\events\EventTypeEvent;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\EventType;
use fredmansky\eventsky\models\EventTypeSite;
use fredmansky\eventsky\models\TicketStatus;
use fredmansky\eventsky\records\EventTypeRecord;
use fredmansky\eventsky\records\EventTypeSiteRecord;
use fredmansky\eventsky\records\TicketStatusRecord;
use yii\db\ActiveQuery;

class TicketStatusService extends Component
{
    public const EVENT_BEFORE_SAVE_EVENT_TYPE = 'beforeSaveEventType';
    
    /** @var array */
    private $statuses;

    public function init()
    {
        parent::init();
    }
    
    public function getAllTicketStatuses(): array
    {
        if ($this->statuses !== null) {
            return $this->statuses;
        }

        $results = $this->createEventTypeQuery()
            ->all();

        $this->statuses = array_map(function($status) {
            return new TicketStatus($status);
        }, $results);

        return $this->statuses;
    }

    private function createEventTypeQuery(): ActiveQuery
    {
        return TicketStatusRecord::find()
            ->orderBy(['name' => SORT_ASC]);
    }
}
