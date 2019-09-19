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
 * EventQuery represents a SELECT SQL statement for events in a way that is independent of DBMS.
 *
// * @property string|string[]|EventType $type The handle(s) of the entry type(s) that resulting entries must have.
 * @author Fredmansky
 * @since 3.0
 * @supports-structure-params
 * @supports-site-params
 * @supports-enabledforsite-param
 * @supports-title-param
 * @supports-slug-param
 * @supports-status-param
 * @supports-uri-param
 * @replace {element} event
 * @replace {elements} events
 * @replace {twig-method} craft.events()
 * @replace {myElement} Event
 * @replace {element-class} \fredmansky\eventsky\elements\Event
 */
class EventTypeQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    /**
     * @var string|string[]|null The title that resulting elements must have.
     * @used-by title()
     */
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

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function beforePrepare(): bool
    {
        // join in the products table
        $this->joinElementTable('eventsky_eventTypes');

        // select the price column
        $this->query->select([
            'eventsky_eventTypes.id',
            'eventsky_eventTypes.description',
        ]);

        if ($this->description) {
            $this->subQuery->andWhere(Db::parseParam('eventsky_events.description', $this->description));
        }

        if ($this->id) {
            $this->subQuery->andWhere(Db::parseParam('eventsky_events.id', $this->id));
        }

        return parent::beforePrepare();
    }
}
