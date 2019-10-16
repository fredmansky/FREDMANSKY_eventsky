<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\records\TicketRecord;
use yii\db\ActiveQuery;

class TicketService extends Component
{
  /** @var array */
  private $tickets;

  public function init()
  {
    parent::init();
  }

  public function getTicketById(int $id): ?ElementInterface
  {
    if (!$id) {
      return null;
    }

    return Craft::$app->getElements()->getElementById($id, Ticket::class);
  }
}
