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
use fredmansky\eventsky\elements\Ticket;

/**
 * Class TicketTypeQuery
 * @package fredmansky\eventsky\elements\db
 */
class TicketTypeQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    public $id;

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
        $this->joinElementTable('eventsky_tickettypes');

        // select the price column
        $this->query->select([
            'eventsky_tickettypes.id',
        ]);

        $this->addWhere('id', 'eventsky_tickettypes.id');

        return parent::beforePrepare();
    }

    // Private Methods
    // =========================================================================

    private function addWhere(string $property, string $column)
    {
      if ($this->{$property}) {
        $this->subQuery->andWhere(Db::parseParam($column, $this->{$property}));
      }
    }
}
