<?php

namespace fredmansky\eventsky\services;

use craft\base\Component;
use craft\db\ActiveRecord;
use craft\db\Query;
use fredmansky\eventsky\models\TicketType;
use fredmansky\eventsky\records\TicketTypeRecord;
use yii\db\ActiveQuery;

class TicketTypeService extends Component
{
    /** @var array */
    private $ticketTypes;

    public function init()
    {
        parent::init();
    }
    
    public function getAllTicketTypes(): array
    {
        if ($this->ticketTypes !== null) {
            return $this->ticketTypes;
        }

        $results = $this->createTicketTypeQuery()
            ->all();

        $this->ticketTypes = array_map(function($result) {
            return new TicketType($result);
        }, $results);
        return $this->ticketTypes;
    }

    public function getTicketTypeById(int $id): ?ActiveRecord
    {
        return $this->createTicketTypeQuery()
            ->where(['=', 'id', $id])
            ->with(['fieldLayout'])
            ->one();
    }

    private function createTicketTypeQuery(): ActiveQuery
    {
        return TicketTypeRecord::find()
            ->orderBy(['name' => SORT_ASC]);
    }
}
