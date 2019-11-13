<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class EventQuery extends ElementQuery
{
    public $typeId;
    public $startDate;
    public $endDate;
    public $postDate;
    public $expiryDate;
    public $needsRegistration;
    public $registrationEnabled;
    public $totalTickets;
    public $hasWaitingList;
    public $waitingListSize;
    
    public function __construct($elementType, array $config = [])
    {
        // Default status
        if (!isset($config['status'])) {
            $config['status'] = ['live'];
        }

        parent::__construct($elementType, $config);
    }

    public function typeId($value)
    {
        $this->typeId = $value;
        return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('eventsky_events');

        $this->query->select([
            'eventsky_events.typeId',
            'eventsky_events.startDate',
            'eventsky_events.endDate',
            'eventsky_events.postDate',
            'eventsky_events.expiryDate',
            'eventsky_events.needsRegistration',
            'eventsky_events.registrationEnabled',
            'eventsky_events.totalTickets',
            'eventsky_events.hasWaitingList',
            'eventsky_events.waitingListSize',
        ]);

        if ($this->typeId) {
            $this->subQuery->andWhere(Db::parseParam('eventsky_events.typeId', $this->typeId));
        }

        return parent::beforePrepare();
    }
}
