<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use fredmansky\eventsky\elements\Ticket;

class TicketQuery extends ElementQuery
{
    public $typeId;
    public $eventId;
    public $statusId;

    public function __construct(string $elementType, array $config = [])
    {
      // Default status
      if (!isset($config['status'])) {
        $config['status'] = TICKET::STATUS_ENABLED;
      }

      parent::__construct($elementType, $config);
    }

    public function typeId($value)
    {
      $this->typeId = $value;
      return $this;
    }

    public function eventId($value)
    {
      $this->eventId = $value;
      return $this;
    }

    public function statusId($value)
    {
      $this->statusId = $value;
      return $this;
    }

    protected function beforePrepare(): bool
    {
        $this->joinElementTable('eventsky_tickets');

        $this->query->select([
          'eventsky_tickets.typeId',
          'eventsky_tickets.eventId',
          'eventsky_tickets.statusId',
        ]);

        if ($this->typeId) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.typeId', $this->typeId));
        }

        if ($this->eventId) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.eventId', $this->eventId));
        }

        if ($this->statusId) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.statusId', $this->statusId));
        }

        return parent::beforePrepare();
    }
}
