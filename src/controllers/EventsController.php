<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\controllers;

use Craft;

use craft\helpers\DateTimeHelper;
use craft\helpers\ElementHelper;
use craft\helpers\StringHelper;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\fields\EventTicketTypeMappingField;
use fredmansky\eventsky\models\EventTicketTypeMapping;
use fredmansky\eventsky\web\assets\editevent\EditEventAsset;
use fredmansky\eventsky\web\assets\availableTicketField\EventTicketTypeMappingAsset;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * The EventTypesController class is a controller that handles various event type related tasks such as
 * displaying, saving, deleting and reordering them in the control panel.
 * Note that all actions in this controller require administrator access in order to execute.
 *
 * @author Fredmansky
 * @since 3.0
 */
class EventsController extends Controller
{

    public function init()
    {
        $this->requireAdmin();
        parent::init();
    }

    public function actionIndex(array $data = []): Response
    {
        $data = [
            'eventTypes' => Eventsky::$plugin->eventType->getAllEventTypes(),
        ];
        return $this->renderTemplate('eventsky/events/index', $data);
    }

    public function actionEdit(int $eventId = null, string $site = null): Response
    {
        $data = [];

        $eventTypes = Eventsky::$plugin->eventType->getAllEventTypes();

        $this->getView()->registerAssetBundle(EditEventAsset::class);
        $this->getView()->registerAssetBundle(EventTicketTypeMappingAsset::class);

        /** @var Event $event */
        $event = null;

        $site = $this->getSiteForNewEvent($site);

        if ($eventId !== null) {
            $event = Eventsky::$plugin->event->getEventById($eventId);

            if (!$event) {
                throw new NotFoundHttpException(Craft::t('eventsky', 'translate.event.notFound'));
            }

            $eventType = $event->getType();
            $data['title'] = trim($event->title) ?: Craft::t('eventsky', 'translate.event.edit');
        } else {
            $request = Craft::$app->getRequest();
            $event = new Event();
            $eventType = $eventTypes[0];
            $event->siteId = $site->id;
            $event->typeId = $request->getQueryParam('typeId', $eventType->id);
            $event->slug = ElementHelper::tempSlug();

            // TODO: implement (SS)
            $event->enabled = true;
            $event->enabledForSite = true;

            $event->setFieldValuesFromRequest('fields');
            $data['title'] = Craft::t('eventsky', 'translate.event.new');
        }

        $data['eventId'] = $eventId;
        $data['eventType'] = $eventType;

        $data['eventTypeOptions'] = array_map(function($eventType) {
            return [
                'label' => $eventType->name,
                'value' => $eventType->id,
            ];
        }, $eventTypes);

        $data['event'] = $event;
        $data['element'] = $event;
        $data['site'] = $site;

        $data['crumbs'] = [
            [
                'label' => Craft::t('eventsky', 'translate.events.cpTitle'),
                'url' => UrlHelper::url('eventsky/events')
            ],
        ];

        $data['saveShortcutRedirect'] = 'eventsky/events'; // TODO: correct URL here (SS)
        $data['redirectUrl'] = 'eventsky/events';
        $data['shareUrl'] = '/admin/eventsky'; // TODO: implement
        $data['saveSourceAction'] = 'entries/save-entry';
        $data['isMultiSiteElement'] = Craft::$app->isMultiSite && count(Craft::$app->getSites()->allSiteIds) > 1;
        $data['canUpdateSource'] = true;

        $data['tabs'] = $this->getTabs($data['eventType']->getFieldLayout());

        $this->prepTicketTypeMappingVariables($data);

        return $this->renderTemplate('eventsky/events/edit', $data);
    }

    public function actionSwitchEventType(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $event = $this->getEventModel();
        $this->populateEventModel($event);

        $data = [];
        $data['event'] = $event;
        $data['element'] = $event;

        if (($response = $this->prepEditEventVariables($data)) !== null) {
            return $response;
        }

        $view = $this->getView();
        $tabsHtml = !empty($data['tabs']) ? $view->renderTemplate('_includes/tabs', $data) : null;
        $fieldsHtml = $view->renderTemplate('eventsky/events/_fields', $data);
        $headHtml = $view->getHeadHtml();
        $bodyHtml = $view->getBodyHtml();

        return $this->asJson(compact(
            'tabsHtml',
            'fieldsHtml',
            'headHtml',
            'bodyHtml'
        ));
    }

    public function actionAddNewTicketType(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $request = Craft::$app->getRequest();

        $handle = $request->getBodyParam('ticketType');
        $ticketType = Eventsky::$plugin->ticketType->getTicketTypeByHandle($handle);
        $ticketTypeMapping = new EventTicketTypeMapping();
        $ticketTypeMapping->setTicketType($ticketType);

        $data = [
            'ticketTypeMapping' => $ticketTypeMapping,
        ];

        $view = $this->getView();
        $fieldHtml = $view->renderTemplate('eventsky/_components/fieldTypes/EventTicketTypeMapping/_ticketTypeBlock', $data);
        $bodyHtml = $view->getBodyHtml();

        return $this->asJson(compact(
            'fieldHtml',
            'bodyHtml'
        ));
    }

    public function actionSave()
    {
        $this->requirePostRequest();

        $event = $this->getEventModel();
        $request = Craft::$app->getRequest();

        // Populate the event with post data
        $this->populateEventModel($event);

        $this->saveEventTicketTypesMappings($event);

        if (!Craft::$app->getElements()->saveElement($event)) {
            if ($request->getAcceptsJson()) {
                return $this->asJson([
                    'success' => false,
                    'errors' => $event->getErrors(),
                ]);
            }

            Craft::$app->getSession()->setError(Craft::t('eventsky', 'translate.event.notSaved'));

            // Send the event back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'event' => $event,
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('eventsky', 'translate.event.saved'));

        return $this->redirectToPostedUrl($event);
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $eventId = Craft::$app->getRequest()->getRequiredBodyParam('id');
        Eventsky::$plugin->event->deleteEventById($eventId);

        return $this->asJson(['success' => true]);
    }

    private function saveEventTicketTypesMappings($event) {
        $request = Craft::$app->getRequest();

        $currentTicketTypeMappings = Eventsky::$plugin->event->getAllTicketTypeMappingsByEventId($event->id);
        $newTicketTypeMappings = $request->getBodyParam('availableTicketTypes') ?? [];

        $oldTicketTypeMappings = array_filter($currentTicketTypeMappings, function ($mapping) use ($newTicketTypeMappings) {
            return !array_key_exists($mapping->tickettypeId, $newTicketTypeMappings);
        });

        $this->deleteOldTicketTypeMappings($oldTicketTypeMappings);
        $this->saveTicketTypeMappings($event, $newTicketTypeMappings);

    }

    private function deleteOldTicketTypeMappings($ticketTypeMappings) {
        foreach ($ticketTypeMappings as $ticketTypeMapping) {
            Eventsky::$plugin->event->deleteEventTicketTypeMapping($ticketTypeMapping);
        }
    }

    private function saveTicketTypeMappings($event, $ticketTypeMappings) {
        foreach ($ticketTypeMappings as $ticketTypeMappingData) {
            $ticketTypeId = $ticketTypeMappingData['typeId'];
            $ticketTypeMapping = $this->getTicketTypeMappingModel($event->id, $ticketTypeId);
            $this->populateTicketTypeMappingModel($ticketTypeMapping, $ticketTypeMappingData);
            Eventsky::$plugin->event->saveEventTicketTypeMapping($ticketTypeMapping);
        }
    }

    private function getTabs($fieldLayout) {
        $tabs = [
            [
                'label' => Craft::t('eventsky', 'translate.events.tab.eventData'),
                'url' => '#' . StringHelper::camelCase('tab' . Craft::t('eventsky', 'translate.events.tab.eventData')),
//                'class' => $hasErrors ? 'error' : null,
            ],
            [
                'label' => Craft::t('eventsky', 'translate.events.tab.tickets'),
                'url' => '#' . StringHelper::camelCase('tab' . Craft::t('eventsky', 'translate.events.tab.tickets')),
//                'class' => $hasErrors ? 'error' : null,
            ],
        ];

        foreach ($fieldLayout->getTabs() as $index => $tab) {
            // Do any of the fields on this tab have errors?
//            $hasErrors = false;
//            if ($event->hasErrors()) {
//                foreach ($tab->getFields() as $field) {
//                    /** @var Field $field */
//                    if ($hasErrors = $event->hasErrors($field->handle . '.*')) {
//                        break;
//                    }
//                }
//            }
            $hasErrors = null;

            $tabs[] = [
                'label' => $tab->name,
                'url' => '#' . StringHelper::camelCase('tab' . $tab->name),
                'class' => $hasErrors ? 'error' : null,
            ];
        }

        return $tabs;
    }

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

    private function prepTicketTypeMappingVariables(array &$data)
    {
        $data['eventTicketTypeMappingField'] = Eventsky::$plugin->fieldService->getFieldByHandle(EventTicketTypeMappingField::FIELD_HANDLE);
        $data['ticketTypes'] = Eventsky::$plugin->ticketType->getAllTicketTypes();
    }

    private function prepEditEventVariables(array &$data)
    {
        $request = Craft::$app->getRequest();

        if (empty($data['event'])) {
            if (empty($data['eventId'])) {
                throw new BadRequestHttpException('Request missing required eventId param');
            }

            $data['event'] = Event::find()
                ->id($data['eventId'])
//                ->siteId($site->id)
                ->anyStatus()
                ->one();

            if (!$data['event']) {
                throw new NotFoundHttpException('Entry not found');
            }
        }

        // Override the event type?
        $typeId = $request->getParam('typeId');

        if (!$typeId) {
            // Default to the section's first entry type
            $typeId = $data['entry']->typeId ?? Eventsky::$plugin->eventType->getAllEventTypes()[0]->id;
        }

        $data['event']->typeId = $typeId;
        $data['eventType'] = $data['event']->getType();

        // Prevent the last entry type's field layout from being used
        $data['event']->fieldLayoutId = null;

        // Define the content tabs
        // ---------------------------------------------------------------------
        $data['tabs'] = $this->getTabs($data['eventType']->getFieldLayout());

        return null;
    }

    private function getEventModel(): Event
    {
        $request = Craft::$app->getRequest();
        $eventId = $request->getBodyParam('eventId');
        $siteId = $request->getBodyParam('siteId');

        if ($eventId) {
            $event = Eventsky::$plugin->event->getEventById($eventId);

            if (!$event) {
                throw new HttpException(404, Craft::t('eventsky', 'translate.event.notFound'));
            }
        } else {
            $event = new Event();

            if ($siteId) {
                $event->siteId = $siteId;
            }
        }

        return $event;
    }

    private function getTicketTypeMappingModel($eventId, $ticketTypeId): EventTicketTypeMapping
    {
        $mapping = Eventsky::$plugin->event->getTicketTypeMapping($eventId, $ticketTypeId);

        if (!$mapping) {
            $mapping = new EventTicketTypeMapping();
            $mapping->eventId = $eventId;
            $mapping->tickettypeId = $ticketTypeId;
        }

        return $mapping;
    }

    private function populateEventModel(Event $event)
    {
        $request = Craft::$app->getRequest();

        // Set the entry attributes, defaulting to the existing values for whatever is missing from the post data
        $event->title = $request->getBodyParam('title', $event->title);
        $event->slug = $request->getBodyParam('slug', $event->slug);
        $event->typeId = $request->getBodyParam('typeId', $event->typeId);
        $event->needsRegistration = $request->getBodyParam('needsRegistration', $event->needsRegistration);
        $event->registrationEnabled = $request->getBodyParam('registrationEnabled', $event->registrationEnabled);
        $event->totalTickets = $request->getBodyParam('totalTickets', $event->totalTickets);
        $event->hasWaitingList = $request->getBodyParam('hasWaitingList', $event->hasWaitingList);
        $event->waitingListSize = $request->getBodyParam('waitingListSize', $event->waitingListSize);

        if (($postDate = $request->getBodyParam('postDate')) !== null) {
            $event->postDate = DateTimeHelper::toDateTime($postDate) ?: null;
        }
        if (($expiryDate = $request->getBodyParam('expiryDate')) !== null) {
            $event->expiryDate = DateTimeHelper::toDateTime($expiryDate) ?: null;
        }
        if (($startDate = $request->getBodyParam('startDate')) !== null) {
            $event->startDate = DateTimeHelper::toDateTime($startDate) ?: null;
        }
        if (($endDate = $request->getBodyParam('endDate')) !== null) {
            $event->endDate = DateTimeHelper::toDateTime($endDate) ?: null;
        }

        $event->enabled = (bool) $request->getBodyParam('enabled', $event->enabled);
        $event->enabledForSite = (bool) $request->getBodyParam('enabledForSite', $event->enabledForSite);

        if (!$event->typeId) {
            // Default to the section's first entry type
            $event->typeId = Eventsky::$plugin->eventType->getAllEventTypes()[0]->id;
        }

        // Prevent the last events type's field layout from being used
        $event->fieldLayoutId = null;

        // save values from custom fields to event
        $fieldsLocation = $request->getParam('fieldsLocation', 'fields');
        $event->setFieldValuesFromRequest($fieldsLocation);
    }

    private function populateTicketTypeMappingModel(EventTicketTypeMapping $mapping, array $data)
    {
        $mapping->limit = $data['limit'];

        if (($registrationStartDate = $data['registrationStart']) !== null) {
            $mapping->registrationStartDate = DateTimeHelper::toDateTime($registrationStartDate) ?: null;
        }

        if (($registrationEndDate = $data['registrationEnd']) !== null) {
            $mapping->registrationEndDate = DateTimeHelper::toDateTime($registrationEndDate) ?: null;
        }
    }
}
