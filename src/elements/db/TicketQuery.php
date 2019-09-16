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
 * TicketQuery represents a SELECT SQL statement for events in a way that is independent of DBMS.
 *
// * @property string|string[]|TicketType $type The handle(s) of the entry type(s) that resulting entries must have.
 * @author Fredmansky
 * @since 3.0
 * @supports-structure-params
 * @supports-site-params
 * @supports-enabledforsite-param
 * @supports-title-param
 * @supports-slug-param
 * @supports-status-param
 * @supports-uri-param
 * @replace {element} ticket
 * @replace {elements} tickets
 * @replace {twig-method} craft.tickets()
 * @replace {myElement} Ticket
 * @replace {element-class} \fredmansky\eventsky\elements\Ticket
 */
class TicketQuery extends ElementQuery
{
    // Properties
    // =========================================================================

    /**
     * @var string|string[]|null The title that resulting elements must have.
     * @used-by title()
     */
    public $title;

    public $description;

    // General parameters
    // -------------------------------------------------------------------------

    /**
     * @var int|int[]|null The entry type ID(s) that the resulting entries must have.
     * @used-by EntryQuery::type()
     * @used-by typeId()
     */
    public $typeId;

    /**
     * @var int|int[]|null The user ID(s) that the resulting entries’ authors must have.
     * @used-by authorId()
     */
    public $authorId;


    // Public Methods
    // =========================================================================

    public function description($value)
    {
        $this->description = $value;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __construct($elementType, array $config = [])
    {
        // Default status
        if (!isset($config['status'])) {
            $config['status'] = ['live'];
        }

        parent::__construct($elementType, $config);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function beforePrepare(): bool
    {
        // join in the products table
        $this->joinElementTable('eventsky_tickets');

        // select the price column
        $this->query->select([
            'eventsky_tickets.description',
        ]);

        if ($this->description) {
            $this->subQuery->andWhere(Db::parseParam('eventsky_tickets.description', $this->description));
        }

        return parent::beforePrepare();
    }
}
