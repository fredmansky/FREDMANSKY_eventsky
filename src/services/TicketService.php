<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\models\TicketType;
use fredmansky\eventsky\records\TicketRecord;
use yii\db\ActiveQuery;

class TicketService extends Component
{
//    public const EVENT_BEFORE_SAVE_TICKET = 'beforeSaveTicket';

    /** @var array */
    private $tickets;

    public function init(): void
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

    public function getTicketsByType(TicketType $ticketType): array
    {
        $results = Ticket::find()
            ->typeId($ticketType->id)
            ->all();

        $this->tickets = [];
        foreach ($results as $ticketStatusRecord) {
            $this->tickets[] = $this->createTicketFromRecord($ticketStatusRecord);
        }

        return $this->tickets;
    }

    public function getTicketsByEvent(Event $event): array
    {
        return Ticket::find()
            ->eventId($event->id)
            ->all();
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
//        $transaction = Craft::$app->getDb()->beginTransaction();
//        try {
//            Craft::$app->getDb()->createCommand()
//                ->softDelete(Table::TICKETS, ['id' => $ticket->id])
//                ->execute();
//
//            $transaction->commit();
//        } catch (\Throwable $e) {
//            $transaction->rollBack();
//            throw $e;
//        }

        $elementsService = Craft::$app->getElements();
        $elementsService->deleteElement($ticket);

        // Clear caches
        $this->tickets = null;

        return true;
    }

    public function getTicketByEventAndStatus($eventId, $statusId): array
    {
        $tickets = Ticket::find()
            ->eventId($eventId)
            ->statusId($statusId)
            ->all();

        return $tickets;
    }

    public function getTicketCountByEventAndStatus($eventId, $statusId): int
    {
        $tickets = $this->getTicketByEventAndStatus($eventId, $statusId);
        return count($tickets);
    }

    private function createTicketQuery(): ActiveQuery
    {
        return TicketRecord::find()
            ->orderBy(['dateCreated' => SORT_ASC]);
    }

    private function createTicketFromRecord(TicketRecord $ticketRecord): Ticket
    {
        return new Ticket($ticketRecord->getAttributes());
    }
}
