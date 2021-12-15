<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\controllers;

use craft\web\Controller;
use yii\web\Response;

/**
 * The EventController class is a controller that handles various event type related tasks such as
 * displaying, saving, deleting and reordering them in the control panel.
 * Note that all actions in this controller require administrator access in order to execute.
 *
 * @author Fredmansky
 */
class EventController extends Controller
{
    public function actionIndex(): Response
    {
        $data = [];
        return $this->renderTemplate('eventsky/events/index', $data);
    }
}