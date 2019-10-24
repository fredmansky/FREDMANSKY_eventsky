<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\controllers;

use Craft;
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
    $ticketContent = null;

    if ($ticketId !== null) {
      $ticket = Eventsky::$plugin->ticket->getTicketById($ticketId);
      $data['ticketContent'] = Eventsky::$plugin->ticket->getTicketContentById($ticketId);

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

      // TODO: implement (SS)
      $ticket->enabled = true;
      $ticket->enabledForSite = true;

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
      [
        'label' => Craft::t('eventsky', 'translate.ticket.tab.event'),
        'url' => '#' . StringHelper::camelCase('tab' . Craft::t('eventsky', 'translate.ticket.tab.event')),
      ],
    ];

    foreach ($ticketType->getFieldLayout()->getTabs() as $index => $tab) {
      $hasErrors = null;

      $data['tabs'][] = [
        'label' => $tab->name,
        'url' => '#' . StringHelper::camelCase('tab' . $tab->name),
        'class' => $hasErrors ? 'error' : null,
      ];

      $data['tabsFields'][]= [
        'tab' => $tab->name,
        'fields' => $tab->getFields(),
      ];
    }

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
    $ticket->handle = $request->getBodyParam('handle');
    $ticket->description = $request->getBodyParam('description');
    $ticket->typeId = $request->getBodyParam('typeId');
    $ticket->eventId = $request->getBodyParam('eventId');
    $ticket->title = $request->getBodyParam('name');
    $ticket->slug = $request->getBodyParam('slug');
    $ticket->typeId = $request->getBodyParam('typeId');
    $ticket->description = $request->getBodyParam('description');

    // save values from custom fields to event
    $ticket->setFieldValuesFromRequest('fields');

    if (($postDate = $request->getBodyParam('postDate')) !== null) {
      $ticket->postDate = DateTimeHelper::toDateTime($postDate) ?: null;
    }

    if ($ticket->postDate === null) {
      $ticket->postDate = DateTimeHelper::currentUTCDateTime();
    }

    if (($expiryDate = $request->getBodyParam('expiryDate')) !== null) {
      $ticket->expiryDate = DateTimeHelper::toDateTime($expiryDate) ?: null;
    }
    if (($startDate = $request->getBodyParam('startDate')) !== null) {
      $ticket->startDate = DateTimeHelper::toDateTime($startDate) ?: null;
    }
    if (($endDate = $request->getBodyParam('endDate')) !== null) {
      $ticket->endDate = DateTimeHelper::toDateTime($endDate) ?: null;
    }

    // get field layout tab content
    $fieldLayoutTabs = $ticket->getFieldLayout()->getTabs();

    foreach ($fieldLayoutTabs as $index => $tab) {
      foreach ($tab->getFields() as $field) {
        $content = $request->getBodyParam($field->handle);
        $ticket[$field->handle] = $content;
      }
    }

    Eventsky::$plugin->ticket->saveTicket($ticket);

    /*
    if (!Craft::$app->getElements()->saveElement($ticket)) {
      if ($request->getAcceptsJson()) {
        return $this->asJson([
          'success' => false,
          'errors' => $ticket->getErrors(),
        ]);
      }

      Craft::$app->getSession()->setError(Craft::t('eventsky', 'translate.ticket.notSaved'));

      // Send the event back to the template
      Craft::$app->getUrlManager()->setRouteParams([
        'ticket' => $ticket,
      ]);

      return null;
    }

    Craft::$app->getSession()->setNotice(Craft::t('eventsky', 'translate.ticket.saved'));*/
    return $this->redirectToPostedUrl($ticket);
  }

  /*
  private function getSiteForNewEvent($site) {
    $sitesService = Craft::$app->getSites();
    $siteIds = $sitesService->allSiteIds;
    if ($site !== null) {
      $siteHandle = $site;
      $site = $sitesService->getSiteByHandle($siteHandle);
      if (!$site) {
        throw new BadRequestHttpException('Invalid site handle: ' . $siteHandle);
      }
    }

    // If there's only one site, go with that
    if ($site === null && count($siteIds) === 1) {
      $site = $sitesService->getSiteById($siteIds[0]);
    }

    // If we still don't know the site, give the user a chance to pick one
    if ($site === null) {
      return $this->renderTemplate('_special/sitepicker', [
        'siteIds' => $siteIds,
        'baseUrl' => "entries/event/new",
      ]);
    }

    return $site;
  }
  */

  public function actionDelete(): Response
  {
    $this->requirePostRequest();
    $this->requireAcceptsJson();

    $ticketId = Craft::$app->getRequest()->getRequiredBodyParam('id');
    Eventsky::$plugin->ticket->deleteTicketById($ticketId);

    return $this->asJson(['success' => true]);
  }
}
