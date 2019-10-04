<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\controllers;

use Craft;

use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\elements\db\EventTypeQuery;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\EventType;
use yii\helpers\VarDumper;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use yii\web\ForbiddenHttpException;

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
class EventTypesController extends Controller
{
  
    public function init()
    {
        $this->requireAdmin();
        parent::init();
    }
    
    public function actionIndex(array $variables = []): Response
    {
        $data = [
            'eventTypes' => Eventsky::$plugin->eventType->getAllEventTypes(),
        ];

        return $this->renderTemplate('eventsky/eventTypes/index', $data);
    }

    public function actionEdit(int $eventTypeId = null, EventType $eventType = null): Response
    {
        $data = [
            'eventTypeId' => $eventTypeId,
            'brandNewEventType' => false,
        ];

        if ($eventTypeId !== null) {
            if ($eventType === null) {
                $eventType = Eventsky::$plugin->eventType->getEventTypeById($eventTypeId);

                if (!$eventType) {
                    throw new NotFoundHttpException('EventType not found');
                }
            }

            $data['title'] = trim($eventType->name) ?: Craft::t('eventsky', 'translate.eventTypes.edit');
        } else {
            if ($eventType === null) {
                $eventType = new EventType();
                $data['brandNewEventType'] = true;
            }
            $data['title'] = Craft::t('eventsky', 'translate.eventTypes.new');
        }

        $data['eventType'] = $eventType;

        $data['crumbs'] = [
            [
                'label' => Craft::t('eventsky', 'translate.eventTypes.cpTitle'),
                'url' => UrlHelper::url('settings/sections')
            ],
        ];

        $data['tabs'] = [
            'settings' => [
                'label' => Craft::t('eventsky', 'translate.eventType.tab.settings'),
                'url' => '#eventtype-settings'
            ],
            'fieldLayout' => [
                'label' => Craft::t('eventsky', 'translate.eventType.tab.fieldlayout'),
                'url' => '#eventtype-fieldlayout'
            ]
        ];

        return $this->renderTemplate('eventsky/eventTypes/edit', $data);
    }
}
