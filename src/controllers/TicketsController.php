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
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\web\assets\editticket\EditTicketAsset;
use craft\helpers\UrlHelper;
use craft\web\Controller;

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
//  public const EVENT_BEFORE_SWITCH_TICKET_TYPE = 'beforeSwitchTicketType';

    public function init()
    {
        $this->requireAdmin();
        parent::init();
    }

    public function actionIndex(array $variables = []): Response
    {
        $data = [
            'ticketTypes' => Eventsky::$plugin->ticketType->getAllTicketTypes(),
            'ticketStatuses' => Eventsky::$plugin->ticketStatus->getAllTicketStatuses(),
            'events' => Eventsky::$plugin->event->getAllEvents(),
        ];

        return $this->renderTemplate('eventsky/tickets/index', $data);
    }

    public function actionEdit(int $ticketId = null, Ticket $ticket = null): Response
    {
        $data = [];

        $ticketTypes = Eventsky::$plugin->ticketType->getAllTicketTypes();
        $events = Eventsky::$plugin->event->getAllEvents();
        $ticketStatuses = Eventsky::$plugin->ticketStatus->getAllTicketStatuses();

        $this->getView()->registerAssetBundle(EditTicketAsset::class);

        if ($ticket) {
            $ticketType = $ticket->getType();
            $event = $ticket->getEvent();
            $status = $ticket->getStatus();
            $data['title'] = trim($ticket->title) ?: Craft::t('eventsky', 'translate.ticket.edit');
        } else if ($ticketId !== null) {
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

        $ticket = $this->getTicketModel();
        $request = Craft::$app->getRequest();

        // Populate the ticket with post data
        $this->populateTicketModel($ticket);

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

        Craft::$app->getSession()->setNotice(Craft::t('eventsky', 'translate.ticket.saved'));
        return $this->redirectToPostedUrl($ticket);
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $ticketId = Craft::$app->getRequest()->getRequiredBodyParam('id');
        Eventsky::$plugin->ticket->deleteTicketById($ticketId);

        return $this->asJson(['success' => true]);
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

    private function populateTicketModel(Ticket $ticket)
    {
        $request = Craft::$app->getRequest();

        // Set the entry attributes, defaulting to the existing values for whatever is missing from the post data
        $ticket->title = $request->getBodyParam('title', $ticket->title);
        $ticket->slug = $request->getBodyParam('slug', $ticket->slug);
        $ticket->typeId = $request->getBodyParam('typeId', $ticket->typeId);
        $ticket->eventId = $request->getBodyParam('eventId', $ticket->eventId);
        $ticket->statusId = $request->getBodyParam('statusId', $ticket->statusId);

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
