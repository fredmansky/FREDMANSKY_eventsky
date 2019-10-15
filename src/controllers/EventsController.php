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
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\EventType;
use fredmansky\eventsky\models\EventTypeSite;
use craft\helpers\UrlHelper;
use craft\web\Controller;

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

    public function actionIndex(array $variables = []): Response
    {
        $data = [];
        return $this->renderTemplate('eventsky/events/index', $data);
    }

    public function actionEdit(int $eventId = null, string $site = null): Response
    {
        $data = [];

        $eventTypes = Eventsky::$plugin->eventType->getAllEventTypes();

        /** @var EventType $eventType */
        $eventType = $eventTypes[0];

        /** @var Event $event */
        $event = null;

        $site = $this->getSiteForNewEvent($site);

        if ($eventId !== null) {
            $event = Eventsky::$plugin->event->getEventById($eventId);

            if (!$event) {
                throw new NotFoundHttpException(Craft::t('eventsky', 'translate.event.notFound'));
            }

            $data['title'] = trim($event->title) ?: Craft::t('eventsky', 'translate.event.edit');
        } else {
            $request = Craft::$app->getRequest();
            $event = new Event();
            $event->siteId = $site->id;
            $event->typeId = $request->getQueryParam('typeId', $eventType->id);
//                $event->authorId = $request->getQueryParam('authorId', Craft::$app->getUser()->getId());
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
                'value' => $eventType->id
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

        $data['tabs'] = [
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
        foreach ($eventType->getFieldLayout()->getTabs() as $index => $tab) {
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

            $data['tabs'][] = [
                'label' => $tab->name,
                'url' => '#' . StringHelper::camelCase('tab' . $tab->name),
                'class' => $hasErrors ? 'error' : null,
            ];
        }

        return $this->renderTemplate('eventsky/events/edit', $data);
    }

    public function actionSave()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $eventId = $request->getBodyParam('eventId');

        if ($eventId) {
            $event = Eventsky::$plugin->event->getEventById($eventId);

            if (!$event) {
                throw new HttpException(404, Craft::t('eventsky', 'translate.event.notFound'));
            }
        } else {
            $event = new Event();
        }


        $event->title = $request->getBodyParam('title');
        $event->slug = $request->getBodyParam('slug');
        $event->typeId = $request->getBodyParam('typeId');
        $event->description = $request->getBodyParam('description');

        // save values from custom fields to event
        $event->setFieldValuesFromRequest('fields');

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

//    public function actionDelete(): Response
//    {
//    }

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
}
