<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\controllers;

use Craft;
use craft\helpers\ElementHelper;
use craft\helpers\StringHelper;
use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\events\TicketSaveEvent;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\web\assets\editticket\EditTicketAsset;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * The TicketsController class is a controller that handles various ticket related tasks such as retrieving, saving,
 * swapping between ticket types, and deleting tickets.
 *
 * @author Fredmansky
 * @since 3.0
 */
class TicketsController extends Controller
{
    const EVENT_SAVE_TICKET = 'saveEventskyTicket';

    protected $allowAnonymous = ['save'];

    public function actionIndex(array $variables = []): Response
    {
        $data = [
            'ticketTypes' => Eventsky::$plugin->ticketType->getAllTicketTypes(),
            'ticketStatuses' => Eventsky::$plugin->ticketStatus->getAllTicketStatuses(),
            'events' => Eventsky::$plugin->event->getAllEvents(),
        ];

        return $this->renderTemplate('eventsky/tickets/index', $data);
    }


    public function actionEdit(int $ticketId = null): Response
    {
        $data = [];

        $ticketTypes = Eventsky::$plugin->ticketType->getAllTicketTypes();
        $events = Eventsky::$plugin->event->getAllEvents();
        $ticketStatuses = Eventsky::$plugin->ticketStatus->getAllTicketStatuses();

        $this->getView()->registerAssetBundle(EditTicketAsset::class);

        /** @var Ticket $ticket */
        $ticket = null;

        if ($ticketId !== null) {
            $ticket = Eventsky::$plugin->ticket->getTicketById($ticketId);

            if (!$ticket) {
                throw new NotFoundHttpException(Craft::t('eventsky', 'translate.ticket.notFound'));
            }

            $ticketType = $ticket->getType();
            $event = $ticket->getEvent();
            $status = $ticket->getStatus();
            $data['title'] = trim($ticket->title) ?: Craft::t('eventsky', 'translate.ticket.edit');
        } else {
            $request = Craft::$app->getRequest();
            $ticket = new Ticket();
            $ticketType = $ticketTypes[0];
            $event = $events[0];
            $status = $ticketStatuses[0];
            $ticket->typeId = $request->getQueryParam('typeId', $ticketType->id);
            $ticket->eventId = $request->getQueryParam('eventId', $event->id);
            $ticket->statusId = $request->getQueryParam('statusId', $status->id);
            $ticket->slug = ElementHelper::tempSlug();

            $ticket->setFieldValuesFromRequest('fields');
            $data['title'] = Craft::t('eventsky', 'translate.ticket.new');
        }

        $data['ticketId'] = $ticketId;
        $data['ticketType'] = $ticketType;
        $data['ticketEvent'] = $event;
        $data['ticketStatus'] = $status;

        $data['ticketTypeOptions'] = array_map(function($ticketType) {
            return [
                'label' => $ticketType->name,
                'value' => $ticketType->id,
            ];
        }, $ticketTypes);

        $data['ticketStatusOptions'] = array_map(function($status) {
            return [
                'label' => $status->name,
                'value' => $status->id,
            ];
        }, $ticketStatuses);

        $data['ticketEventOptions'] = array_map(function($event) {
            return [
                'label' => $event->title,
                'value' => $event->id,
            ];
        }, $events);

        $data['ticket'] = $ticket;
        $data['element'] = $ticket;

        $data['crumbs'] = [
            [
                'label' => Craft::t('eventsky', 'translate.tickets.cpTitle'),
                'url' => UrlHelper::url('eventsky/tickets')
            ],
        ];

        $data['saveShortcutRedirect'] = 'eventsky/tickets'; // TODO: correct URL here (SS)
        $data['redirectUrl'] = 'eventsky/tickets';
        $data['shareUrl'] = '/admin/eventsky'; // TODO: implement
        $data['saveSourceAction'] = 'entries/save-entry';
        $data['isMultiSiteElement'] = false;
        $data['canUpdateSource'] = true;

        $data['tabs'] = $this->getTabs($data['ticketType']->getFieldLayout());

        return $this->renderTemplate('eventsky/tickets/edit', $data);
    }

    public function actionSwitchTicketType(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $ticket = $this->getTicketModel();
        $this->populateTicketModel($ticket);

        $data = [];
        $data['ticket'] = $ticket;
        $data['element'] = $ticket;

        $this->prepEditTicketVariables($data);
        $view = $this->getView();

        $tabsHtml = !empty($data['tabs']) ? $view->renderTemplate('_includes/tabs', $data) : null;
        $fieldsHtml = $view->renderTemplate('eventsky/tickets/_fields', $data);

        return $this->asJson(compact(
            'tabsHtml',
            'fieldsHtml'
        ));
    }

    public function actionSave()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $markedAsSpam = false;
        $honeypot = $request->getBodyParam('eventsky');

        if ($honeypot != '') {
            $markedAsSpam = true;
        }

        $eventIds = $request->getBodyParam('eventIds');
        $eventId = $request->getBodyParam('eventId');

        if (!$request->isCpRequest) {
            $hash = $request->getBodyParam('eventHash');

            $eventIdForSpamProtection = is_array($eventIds) ? $eventIds[0] : $eventId;
            $eventHash = Eventsky::getInstance()->event->getEventHashBy($eventIdForSpamProtection);

            if ($eventHash !== $hash) {
                $markedAsSpam = true;
            }
        }

        $ticket = $this->getTicketModel();

        // Populate the ticket with post data
        $this->populateTicketModel($ticket, $markedAsSpam);

        if (is_array($eventIds)) {
            $ticket->id = null;
            $this->saveMultipleTickets($ticket, $eventIds, $markedAsSpam);
        } else if ($eventId) {
            return $this->saveSingleTicket($ticket, $request, $markedAsSpam);
        } else {
            throw new HttpException(404, Craft::t('eventsky', 'translate.event.notFound'));
        }
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $ticketId = Craft::$app->getRequest()->getRequiredBodyParam('id');
        Eventsky::$plugin->ticket->deleteTicketById($ticketId);

        return $this->asJson(['success' => true]);
    }

    private function saveMultipleTickets($ticket, $eventIds, $markedAsSpam) {
        $errors = [];
        $eventIds = array_unique($eventIds);
        $allEventIds = Eventsky::$plugin->event->getAllEventIds();
        $tickets = [];

        foreach ($eventIds as $eventId) {
            if (!in_array($eventId, $allEventIds)) {
                $errors[$eventId] = Craft::t('eventsky', 'translate.ticket.notSaved');
            }
        }

        if(!empty($errors)) {
            throw new HttpException(404, Craft::t('eventsky', 'translate.ticket.notSaved'));
        }

        foreach ($eventIds as $eventId) {
            $ticket->id = null;
            $ticket->eventId = $eventId;

            $newTicket = clone $ticket;
            if (!Craft::$app->getElements()->saveElement($newTicket)) {
                throw new HttpException(404, Craft::t('eventsky', 'translate.ticket.notSaved'));
            }

            $tickets[] = $newTicket;

            if ($this->hasEventHandlers(self::EVENT_SAVE_TICKET)) {
                $this->trigger(self::EVENT_SAVE_TICKET, new TicketSaveEvent([
                    'ticket' => $ticket,
                    'eventId' => $eventId,
                    'isNew' => true,
                ]));
            }
        }

        if(!$markedAsSpam) {
            $this->sendMails($tickets);
        }
        return true;
    }

    private function saveSingleTicket($ticket, $request, $markedAsSpam) {
        $isNew = !(bool) $ticket->id;

        if (!Craft::$app->getElements()->saveElement($ticket)) {
            if ($request->getAcceptsJson()) {
                return $this->asJson([
                    'success' => false,
                    'errors' => $ticket->getErrors(),
                ]);
            }

            Craft::$app->getSession()->setError(Craft::t('eventsky', 'translate.ticket.notSaved'));

            Craft::$app->getUrlManager()->setRouteParams([
                'ticket' => $ticket,
            ]);

            return null;
        }

        if ($this->hasEventHandlers(self::EVENT_SAVE_TICKET)) {
            $this->trigger(self::EVENT_SAVE_TICKET, new TicketSaveEvent([
                'ticket' => $ticket,
                'eventId' => $ticket->eventId,
                'isNew' => $isNew,
            ]));
        }

        if($isNew && !$markedAsSpam) {
            $this->sendMails([$ticket]);
        }

        if (!$request->isCpRequest && $request->getAcceptsJson()) {
            return $this->asJson([
                'success' => true,
            ]);
        }

        if ($request->isCpRequest) {
            Craft::$app->getSession()->setNotice(Craft::t('eventsky', 'translate.ticket.saved'));
        }

        return $this->redirectToPostedUrl($ticket);
    }

    private function sendMails(array $tickets) {
        $this->sendAdminMails($tickets);
        $this->sendUserMail($tickets);
    }

    private function sendUserMail(array $tickets) {
        $ticket = $tickets[0];

        if (!$tickets) {
            return;
        }

        $emailNotification = $ticket->getType()->getEmailNotification();

        if($emailNotification && $ticket->email) {
            $from = $emailNotification->fromEmail;
            $to = $ticket->email;
            $replyTo = $emailNotification->replyToEmail;
            $subject = $emailNotification->subject;

            $bodyData = [];
            $bodyData['tickets'] = $tickets;
            $body = Craft::$app->getView()->renderString($emailNotification->textContent, $bodyData);

            Eventsky::$plugin->mail->sendMail($from, $to, $replyTo, $subject, $body);
        }
    }

    private function sendAdminMails(array $tickets) {

        foreach ($tickets as $ticket) {
            $event = $ticket->getEvent();
            $eventType = $event->getType();
            $emailNotification = $event->getEmailNotification() ?? $eventType->getEmailNotification() ?? null;
            $emailString = '';

            if ($event->emailNotificationAdminEmails) {
                $emailString = $event->emailNotificationAdminEmails;
            } else if ($eventType->emailNotificationAdminEmails) {
                $emailString = $eventType->emailNotificationAdminEmails;
            }

            $emails = preg_split('/\r\n|\r|\n/', $emailString);

            if ($emailNotification && $emailString) {
                $from = $emailNotification->fromEmail;
                $replyTo = $emailNotification->replyToEmail;
                $subject = $emailNotification->subject;
                $bodyData = [];
                $bodyData['ticket'] = $ticket;
                $bodyData['event'] = $event;
                $body = Craft::$app->getView()->renderString($emailNotification->textContent, $bodyData);

                foreach ($emails as $email) {
                    $to = $email;
                    Eventsky::$plugin->mail->sendMail($from, $to, $replyTo, $subject, $body);
                }
            }
        }
    }

    private function getTabs($fieldLayout) {
        $tabs = [
            [
                'label' => Craft::t('eventsky', 'translate.ticket.tab.ticketData'),
                'url' => '#' . StringHelper::camelCase('tab' . Craft::t('eventsky', 'translate.ticket.tab.ticketData')),
            ],
        ];

        foreach ($fieldLayout->getTabs() as $index => $tab) {
            $hasErrors = null;

            $tabs[] = [
                'label' => $tab->name,
                'url' => '#' . StringHelper::camelCase('tab' . $tab->name),
                'class' => $hasErrors ? 'error' : null,
            ];
        }

        return $tabs;
    }

    private function getTicketModel(): Ticket
    {
        $request = Craft::$app->getRequest();
        $ticketId = $request->getBodyParam('ticketId');

        if ($ticketId) {
            $ticket = Eventsky::$plugin->ticket->getTicketById($ticketId);

            if (!$ticket) {
                throw new HttpException(404, Craft::t('eventsky', 'translate.ticket.notFound'));
            }
        } else {
            $ticket = new Ticket();
        }

        return $ticket;
    }

    private function populateTicketModel(Ticket $ticket, bool $markedAsSpam)
    {
        $request = Craft::$app->getRequest();

        // Set the entry attributes, defaulting to the existing values for whatever is missing from the post data
        $ticket->title = $request->getBodyParam('title', $ticket->title);
        $ticket->slug = $request->getBodyParam('slug', $ticket->slug);
        $ticket->typeId = $request->getBodyParam('typeId', $ticket->typeId);
        $ticket->eventId = $request->getBodyParam('eventId', $ticket->eventId);
        $ticket->statusId = $request->getBodyParam('statusId', $ticket->statusId);
        $ticket->email = $request->getBodyParam('email', $ticket->email);

        if (!$ticket->typeId) {
            // Default to the first ticket type
            $ticket->typeId = Eventsky::$plugin->ticketType->getAllTicketTypes()[0]->id;
        }
        if (!$ticket->eventId) {
            // Default to the first event
            $ticket->eventId = Eventsky::$plugin->event->getAllEvents()[0]->id;
        }
        if (!$ticket->statusId) {
            // Default to the first status
            $ticket->statusId = Eventsky::$plugin->ticketStatus->getAllTicketStatuses()[0]->id;
        }

        if ($markedAsSpam) {
            $ticket->enabled = false;
        }

        // Prevent the last entry type's field layout from being used
        $ticket->fieldLayoutId = null;

        $fieldsLocation = $request->getParam('fieldsLocation', 'fields');
        $ticket->setFieldValuesFromRequest($fieldsLocation);
    }

    private function prepEditTicketVariables(array &$data)
    {
        $ticketType = $data['ticket']->getType();
        $data['ticketType'] = $ticketType;
        $data['tabs'] = $this->getTabs($data['ticketType']->getFieldLayout());

        $data['ticketEventOptions'] = array_map(function($event) {
            return [
                'label' => $event->title,
                'value' => $event->id,
            ];
        }, Eventsky::$plugin->event->getAllEvents());
    }
}
