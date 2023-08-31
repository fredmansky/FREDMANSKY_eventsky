<?php

namespace fredmansky\eventsky\services;

use craft\base\Component;
use fredmansky\eventsky\models\TicketStatus;
use fredmansky\eventsky\records\TicketStatusRecord;
use yii\db\ActiveQuery;

class TicketStatusService extends Component
{
    public const EVENT_BEFORE_SAVE_EVENT_TYPE = 'beforeSaveEventType';
    
    /** @var array */
    private $statuses;

    public function init(): void
    {
        parent::init();
    }
    
    public function getAllTicketStatuses(): array
    {
        if ($this->statuses !== null) {
            return $this->statuses;
        }

        $results = $this->createTicketStatusQuery()
            ->all();

        $this->statuses = [];
        foreach ($results as $ticketStatusRecord) {
            $this->statuses[] = $this->createTicketStatusFromRecord($ticketStatusRecord);
        }

        return $this->statuses;
    }

    private function createTicketStatusQuery(): ActiveQuery
    {
        return TicketStatusRecord::find()
            ->orderBy(['name' => SORT_ASC]);
    }

    private function createTicketStatusFromRecord(TicketStatusRecord $ticketStatusRecord): TicketStatus
    {
        return new TicketStatus($ticketStatusRecord->getAttributes());
    }
}
