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
            'eventTypes' => Eventsky::$plugin->eventType->all(),
        ];

        return $this->renderTemplate('eventsky/eventTypes/index', $data);
    }

    public function actionEdit(int $eventTypeId = null, EventTypeTODELETE $eventType = null): Response
    {
        $data = [
            'eventTypeId' => $eventTypeId,
            'brandNewSection' => false,
        ];

        if ($eventTypeId !== null) {
            if ($eventType === null) {
                $eventType = Eventsky::$plugin->eventType->byId($eventTypeId);

                if (!$eventType) {
                    throw new NotFoundHttpException('EventType not found');
                }
            }

            $data['title'] = trim($eventType->name) ?: Craft::t('app', 'Edit Section');
        } else {
            if ($eventType === null) {
                $eventType = new EventType();
                $data['brandNewSection'] = true;
            }
            $data['title'] = Craft::t('app', 'Create a new section');
        }

        $data['eventType'] = $eventType;

        $variables['crumbs'] = [
            [
                'label' => Craft::t('app', 'Settings'),
                'url' => UrlHelper::url('settings')
            ],
            [
                'label' => Craft::t('app', 'Sections'),
                'url' => UrlHelper::url('settings/sections')
            ],
        ];

        return $this->renderTemplate('eventsky/eventTypes/edit', $data);
    }

}
