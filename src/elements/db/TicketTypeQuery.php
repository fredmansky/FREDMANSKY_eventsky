<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\elements\db;

use Craft;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;

/**
 * Class TicketTypeQuery
 * @package fredmansky\eventsky\elements\db
 */
class TicketTypeQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    public $title;
    public $description;
    public $id;

    public function description($value)
    {
      $this->description = $value;
      return $this;
    }

    public function id($value)
    {
      $this->id = $value;
      return $this;
    }

    // Public Methods
    // =========================================================================

    protected function beforePrepare(): bool
    {
        // join in the products table
        $this->joinElementTable('eventsky_ticketTypes');

        // select the price column
        $this->query->select([
            'eventsky_ticketTypes.id',
            'eventsky_ticketTypes.description',
        ]);

        if ($this->description) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.description', $this->description));
        }

        if ($this->id) {
          $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.id', $this->id));
        }
        return parent::beforePrepare();
    }
}
