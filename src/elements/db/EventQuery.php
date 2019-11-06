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
use fredmansky\eventsky\elements\Event;

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
class EventQuery extends ElementQuery
{
    public $title;
    public $description;
    public $typeId;
    public $authorId;
    public $postDate;
    public $expiryDate;

    public function description($value)
    {
        $this->description = $value;

        return $this;
    }

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
        // join in the event table
        $this->joinElementTable('eventsky_events');

        $this->query->select([
            'eventsky_events.typeId',
            'eventsky_events.description',
            'eventsky_events.startDate',
            'eventsky_events.endDate',
            'eventsky_events.postDate',
            'eventsky_events.expiryDate',
        ]);

        if ($this->description) {
            $this->subQuery->andWhere(Db::parseParam('eventsky_events.description', $this->description));
        }

        return parent::beforePrepare();
    }
}
