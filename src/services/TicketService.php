<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\db\ActiveRecord;
use craft\db\Query;
use craft\events\EntryTypeEvent;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use craft\records\FieldLayout;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\events\TicketEvent;
use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\models\TicketType;
use fredmansky\eventsky\records\TicketRecord;
use fredmansky\eventsky\records\TicketTypeRecord;
use yii\db\ActiveQuery;

class TicketService extends Component
{
  public const EVENT_BEFORE_SAVE_TICKET = 'beforeSaveTicketType';

  /** @var array */
  private $tickets;

  public function init()
  {
    parent::init();
  }

  public function getAllTickets(): array
  {
    if ($this->tickets !== null) {
      return $this->tickets;
    }

    $condition = ['eventsky_tickets.dateDeleted' => null];

    $results = $this->createTicketQuery()
      ->where($condition)
      ->all();

    $this->tickets = array_map(function($result) {
      return new Ticket($result);
    }, $results);
    return $this->tickets;
  }

  public function getTicketById(int $id): ?Ticket
  {
    $result = $this->createTicketQuery()
      ->where(['=', 'id', $id])
      ->one();

    if ($result) {
      return new Ticket($result);
    }

    return null;
  }

  public function saveTicket(Ticket $ticket, bool $runValidation = true)
  {
    $isNewTicket = !$ticket->id;

    if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_TICKET)) {
      $this->trigger(self::EVENT_BEFORE_SAVE_TICKET, new TicketEvent([
        'ticket' => $ticket,
        'isNew' => $isNewTicket
      ]));
    }

    if ($runValidation && !$ticket->validate()) {
      \Craft::info('Ticket not saved due to validation error.', __METHOD__);
      return false;
    }

    if ($isNewTicket) {
      $ticket->uid = StringHelper::UUID();
    } else if (!$ticket->uid) {
      $ticket->uid = Db::uidById(Table::TICKET_TYPES, $ticket->id);
    }

    $ticketRecord = TicketRecord::find()
      ->where(['=', 'id', $ticket->id])
      ->one();

    if (!$ticketRecord) {
      $ticketRecord = new TicketRecord();
    }

    $fieldLayout = $ticket->getFieldLayout();
    \Craft::$app->getFields()->saveLayout($fieldLayout);

    // $ticketRecord->fieldLayoutId = (int) $fieldLayout->id;
    $ticketRecord->name = $ticket->name;
    $ticketRecord->handle = $ticket->handle;
    $ticketRecord->description = $ticket->description;
    $ticketRecord->typeId = $ticket->typeId;
    $ticketRecord->startDate = $ticket->startDate;
    $ticketRecord->endDate = $ticket->endDate;
    $ticketRecord->postDate = $ticket->postDate;
    $ticketRecord->expiryDate = $ticket->expiryDate;
    // $ticketRecord->fieldLayoutId = $ticket->fieldLayoutId;
    $ticketRecord->uid = $ticket->uid;
    // $ticketRecord->setFieldLayout($fieldLayout);
    $ticketRecord->save();

    // @TODO add exceptions when saving is failing
    return true;
  }

  public function deleteTicketById(int $id): bool
  {
    $ticket = $this->getTicketById($id);

    if (!$ticket) {
      return false;
    }

    return $this->deleteTicket($ticket);
  }

  public function deleteTicket(Ticket $ticket): bool
  {
    $transaction = Craft::$app->getDb()->beginTransaction();
    try {
      Craft::$app->getDb()->createCommand()
        ->softDelete(Table::TICKETS, ['id' => $ticket->id])
        ->execute();

      $transaction->commit();
    } catch (\Throwable $e) {
      $transaction->rollBack();
      throw $e;
    }

    // Clear caches
    $this->$ticket = null;

    return true;
  }

  private function createTicketQuery(): ActiveQuery
  {
    return TicketRecord::find();
  }
}
