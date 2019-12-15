<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\models;

use craft\base\Model;
use fredmansky\eventsky\Eventsky;
use yii\base\InvalidConfigException;

/**
 * EventTicketTypeMapping model class.
 *
 * @author Fredmansky
 * @since 3.0
 *
 * @property string $cpEditUrl
 */
class EventTicketTypeMapping extends Model
{
    public $id;
    public $tickettypeId;
    public $eventId;
    public $limit;
    public $isFree;
    public $price;
    public $registrationStartDate;
    public $registrationEndDate;
    public $dateCreated;
    public $dateUpdated;
    public $uid;

    /** @var TicketType */
    private $ticketType;

    public function getTicketType(): TicketType
    {
        if ($this->ticketType !== null) {
            return $this->ticketType;
        }

        if (!$this->tickettypeId) {
            throw new InvalidConfigException('Event ticket type mapping is missing its ticket type ID');
        }

        if (($this->ticketType = Eventsky::$plugin->ticketType->getTicketTypeById($this->tickettypeId)) === null) {
            throw new InvalidConfigException('Invalid ticket type ID: ' . $this->tickettypeId);
        }

        return $this->ticketType;
    }

    public function setTicketType(TicketType $ticketType)
    {
        $this->tickettypeId = $ticketType->id;
        $this->ticketType = $ticketType;
    }

    public function datetimeAttributes(): array
    {
        $attributes = [];

        $attributes[] = 'registrationStartDate';
        $attributes[] = 'registrationEndDate';
        $attributes[] = 'dateCreated';
        $attributes[] = 'dateUpdated';

        return $attributes;
    }
}
