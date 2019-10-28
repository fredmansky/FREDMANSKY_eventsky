<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\controllers;

use Craft;
// use fredmansky\eventsky\events\TicketEvent;
use craft\base\Element;
use craft\base\Field;
use craft\base\Plugin;
use craft\db\Query;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use craft\helpers\ElementHelper;
use craft\helpers\StringHelper;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use craft\models\Site;
use craft\web\assets\editentry\EditEntryAsset;
use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\TicketType;
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
      'tickets' => Eventsky::$plugin->ticket->getAllTickets(),
      'ticketTypes' => Eventsky::$plugin->ticketType->getAllTicketTypes(),
    ];

    return $this->renderTemplate('eventsky/tickets/index', $data);
  }

  public function actionEdit(int $ticketId = null): Response
  {
    $data = [];

    $ticketTypes = Eventsky::$plugin->ticketType->getAllTicketTypes();
    if (!$ticketTypes) {
      throw new NotFoundHttpException(Craft::t('eventsky', 'translate.ticketTypes.notFound'));
    }

//    $ticketEvents = Eventsky::$plugin->events->getAllEvents();
//    if (!$ticketEvents) {
//      throw new NotFoundHttpException(Craft::t('eventsky', 'translate.events.notFound'));
//    }

    /** @var Ticket $ticket */
    $ticket = null;

    if ($ticketId !== null) {
      $ticket = Eventsky::$plugin->ticket->getTicketById($ticketId);

      if (!$ticket) {
        throw new NotFoundHttpException(Craft::t('eventsky', 'translate.ticket.notFound'));
      }

      $ticketType = $ticket->getType();
//      $ticketEvent = $ticket->getEvent();
      $data['title'] = trim($ticket->name) ?: Craft::t('eventsky', 'translate.ticket.edit');
    } else {
      $request = Craft::$app->getRequest();
      $ticket = new Ticket();
      $ticketType = $ticketTypes[0];
//      $ticketEvent = $ticketEvents[0];
      $ticket->typeId = $request->getQueryParam('typeId', $ticketType->id);
      $ticket->slug = ElementHelper::tempSlug();

      $ticket->setFieldValuesFromRequest('fields');
      $data['title'] = Craft::t('eventsky', 'translate.ticket.new');
    }

    $data['ticketId'] = $ticketId;
    $data['ticketType'] = $ticketType;
//    $data['ticketEvent'] = $ticketEvent;

    $data['ticketTypeOptions'] = array_map(function($ticketType) {
      return [
        'label' => $ticketType->name,
        'value' => $ticketType->id
      ];
    }, $ticketTypes);

//    $data['ticketEventOptions'] = array_map(function($ticketEvent) {
//      return [
//        'label' => $ticketEvent->name,
//        'value' => $ticketEvent->id
//      ];
//    }, $ticketEvents);

    $data['ticket'] = $ticket;
    $data['element'] = $ticket;

    $data['crumbs'] = [
      [
        'label' => Craft::t('eventsky', 'translate.ticket.cpTitle'),
        'url' => UrlHelper::url('eventsky/tickets')
      ],
    ];

    $data['saveShortcutRedirect'] = 'eventsky/tickets'; // TODO: correct URL here (SS)
    $data['redirectUrl'] = 'eventsky/tickets';
    $data['shareUrl'] = '/admin/eventsky'; // TODO: implement
    $data['saveSourceAction'] = 'entries/save-entry';
    $data['isMultiSiteElement'] = Craft::$app->isMultiSite && count(Craft::$app->getSites()->allSiteIds) > 1;
    $data['canUpdateSource'] = true;

    $data['tabs'] = [
      [
        'label' => Craft::t('eventsky', 'translate.ticket.tab.ticketData'),
        'url' => '#' . StringHelper::camelCase('tab' . Craft::t('eventsky', 'translate.ticket.tab.ticketData')),
      ],
    ];

    foreach ($ticketType->getFieldLayout()->getTabs() as $index => $tab) {
      $hasErrors = null;

      $data['tabs'][] = [
        'label' => $tab->name,
        'url' => '#' . StringHelper::camelCase('tab' . $tab->name),
        'class' => $hasErrors ? 'error' : null,
      ];
    }


//    // Multiple ticket types?
//    if (count($ticketTypes) > 1) {
//      $variables['showTicketTypes'] = true;
//
//      foreach ($ticketTypes as $ticketType) {
//        $variables['ticketTypeOptions'][] = [
//          'label' => Craft::t('site', $ticketType->name),
//          'value' => $ticketType->id
//        ];
//      }
//
//      $this->trigger(self::EVENT_BEFORE_SWITCH_TICKET_TYPE, new TicketEvent([
//        'ticket' => $ticket,
//        'ticketType' => $ticketType,
//        'isNew' => false,
//        'switchType' => true
//      ]));
//    } else {
//      $variables['showTicketTypes'] = false;
//    }

    return $this->renderTemplate('eventsky/tickets/edit', $data);
  }

  public function actionSave(): Response
  {
    $this->requirePostRequest();

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

    $ticket->id = $request->getBodyParam('ticketId');
    $ticket->name = $request->getBodyParam('name');
    // todo: always update handle or keep first handle even if title changes?
    $ticket->handle = StringHelper::camelCase($ticket->name);
    $ticket->status = $request->getBodyParam('status');
    $ticket->typeId = $request->getBodyParam('typeId');
    $ticket->eventId = $request->getBodyParam('eventId');
    $ticket->title = $request->getBodyParam('name');
    $ticket->slug = $request->getBodyParam('slug');

    // save values from custom fields to event
    $ticket->setFieldValuesFromRequest('fields');

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
}
