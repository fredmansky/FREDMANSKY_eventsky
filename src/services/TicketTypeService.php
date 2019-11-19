<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\events\TicketTypeEvent;
use fredmansky\eventsky\models\TicketType;
use fredmansky\eventsky\records\TicketTypeRecord;
use yii\db\ActiveQuery;
use fredmansky\eventsky\Eventsky;

class TicketTypeService extends Component
{
    public const EVENT_BEFORE_SAVE_TICKET_TYPE = 'beforeSaveTicketType';

    /** @var array */
    private $ticketTypes;

    public function init()
    {
        parent::init();
    }

    public function getAllTicketTypes(): array
    {
        if ($this->ticketTypes !== null) {
            return $this->ticketTypes;
        }

        $condition = ['eventsky_tickettypes.dateDeleted' => null];
        $results = $this->createTicketTypeQuery()
            ->where($condition)
            ->all();

        $this->ticketTypes = array_map(function($result) {
            return new TicketType($result);
        }, $results);
        return $this->ticketTypes;
    }

    public function getTicketTypeById(int $id): ?TicketType
    {
        $result = $this->createTicketTypeQuery()
            ->where(['=', 'id', $id])
            ->with(['fieldLayout'])
            ->one();

        if ($result) {
            return new TicketType($result);
        }

        return null;
    }

    public function getTicketTypeByHandle(string $handle): ?TicketType
    {
        $result = $this->createTicketTypeQuery()
            ->where(['=', 'handle', $handle])
            ->one();

        if ($result) {
            return new TicketType($result);
        }

        return null;
    }

    public function saveTicketType(TicketType $ticketType, bool $runValidation = true)
    {
        $isNewTicketType = !$ticketType->id;

        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_TICKET_TYPE)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_TICKET_TYPE, new TicketTypeEvent([
                'ticketType' => $ticketType,
                'isNew' => $isNewTicketType
            ]));
        }

        if ($runValidation && !$ticketType->validate()) {
            \Craft::info('Ticket Type not saved due to validation error.', __METHOD__);
            return false;
        }

        if ($isNewTicketType) {
            $ticketType->uid = StringHelper::UUID();
        } else if (!$ticketType->uid) {
            $ticketType->uid = Db::uidById(Table::TICKET_TYPES, $ticketType->id);
        }

        $ticketTypeRecord = TicketTypeRecord::find()
            ->where(['=', 'id', $ticketType->id])
            ->one();

        if (!$ticketTypeRecord) {
            $ticketTypeRecord = new TicketTypeRecord();
        }

        $fieldLayout = $ticketType->getFieldLayout();
        \Craft::$app->getFields()->saveLayout($fieldLayout);

//        $ticketTypeRecord->fieldLayoutId = (int) $fieldLayout->id;
        $ticketTypeRecord->name = $ticketType->name;
        $ticketTypeRecord->handle = $ticketType->handle;
//        $ticketTypeRecord->fieldLayoutId = $ticketType->fieldLayoutId;
        $ticketTypeRecord->setFieldLayout($fieldLayout);
        $ticketTypeRecord->uid = $ticketType->uid;
        $ticketTypeRecord->save();

        // @TODO add exceptions when saving is failing
        return true;
    }

    public function deleteTicketTypeById(int $id): bool
    {
        $ticketType = $this->getTicketTypeById($id);

        if (!$ticketType) {
            return false;
        }

        return $this->deleteTicketType($ticketType);
    }

    public function deleteTicketType(TicketType $ticketType): bool
    {
        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            $tickets = Eventsky::$plugin->ticket->getTicketsByType($ticketType);

            // Delete all tickets of ticket type
            foreach ($tickets as $ticket) {
                Eventsky::$plugin->ticket->deleteTicket($ticket);
            }

            // Delete field layout of ticket type
            if ($ticketType->fieldLayoutId) {
                Craft::$app->getFields()->deleteLayoutById($ticketType->fieldLayoutId);
            }

            // Delete the ticket type
            Craft::$app->getDb()->createCommand()
                ->softDelete(Table::TICKET_TYPES, ['id' => $ticketType->id])
                ->execute();

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        // Clear caches
        $this->ticketTypes = null;

        return true;
    }

    private function createTicketTypeQuery(): ActiveQuery
    {
        return TicketTypeRecord::find()
            ->orderBy(['name' => SORT_ASC]);
    }
}
