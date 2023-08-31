<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\elements\db;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\EventType;
use yii\base\InvalidConfigException;
use yii\helpers\Console;

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
    public $emailNotificationIdAdmin;
    public $emailNotificationAdminEmails;

    public function __construct($elementType, array $config = [])
    {
        // Default status
        if (!isset($config['status'])) {
            $config['status'] = [Event::STATUS_LIVE];
        }

        parent::__construct($elementType, $config);
    }

    public function typeId($value)
    {
        $this->typeId = $value;
        return $this;
    }

    public function type($value)
    {
         if ($value instanceof EventType) {
             $this->typeId = $value->id;
         } else if ($value !== null) {
             $this->typeId = Eventsky::$plugin->eventType->getEventTypeByHandle($value);

             if (!$this->typeId) {
                 throw new InvalidConfigException('Invalid event type: ' . $value);
             }
         } else {
             $this->typeId = null;
         }

        return $this;
    }

    public function startDate($value)
    {
        $this->startDate = $value;
        return $this;
    }

    public function endDate($value)
    {
        $this->endDate = $value;
        return $this;
    }

    protected function statusCondition(string $status): mixed
    {
        $currentTimeDb = Db::prepareDateForDb(new \DateTime());

        switch ($status) {
            case Event::STATUS_LIVE:
                return [
                    'and',
                    [
                        'elements.enabled' => true,
                        'elements_sites.enabled' => true
                    ],
                    ['<=', 'eventsky_events.postDate', $currentTimeDb],
                    [
                        'or',
                        ['eventsky_events.expiryDate' => null],
                        ['>', 'eventsky_events.expiryDate', $currentTimeDb]
                    ]
                ];
            case Event::STATUS_PENDING:
                return [
                    'and',
                    [
                        'elements.enabled' => true,
                        'elements_sites.enabled' => true,
                    ],
                    ['>', 'eventsky_events.postDate', $currentTimeDb]
                ];
            case Event::STATUS_EXPIRED:
                return [
                    'and',
                    [
                        'elements.enabled' => true,
                        'elements_sites.enabled' => true
                    ],
                    ['not', ['eventsky_events.expiryDate' => null]],
                    ['<=', 'eventsky_events.expiryDate', $currentTimeDb]
                ];
            default:
                return parent::statusCondition($status);
        }
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
            'eventsky_events.emailNotificationIdAdmin',
            'eventsky_events.emailNotificationAdminEmails',
        ]);

        if ($this->typeId) {
            $this->subQuery->andWhere(Db::parseParam('eventsky_events.typeId', $this->typeId));
        }

        if ($this->startDate) {
            $this->subQuery->andWhere(Db::parseParam('eventsky_events.startDate', $this->startDate));
        }

        if ($this->endDate) {
            $this->subQuery->andWhere(Db::parseParam('eventsky_events.endDate', $this->endDate));
        }

        return parent::beforePrepare();
    }
}
