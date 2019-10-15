<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\controllers;

use Craft;

use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\elements\db\TicketTypeQuery;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\TicketType;
use yii\helpers\VarDumper;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use yii\web\ForbiddenHttpException;

use yii\web\NotFoundHttpException;
use yii\web\Response;


class TicketTypesController extends Controller
{

  public function init()
  {
    $this->requireAdmin();
    parent::init();
  }

  public function actionIndex(array $variables = []): Response
  {
    $data = [
      'ticketTypes' => Eventsky::$plugin->ticketType->getAllTicketTypes(),
    ];

    return $this->renderTemplate('eventsky/ticketTypes/index', $data);
  }

  public function actionEdit(int $ticketTypeId = null, TicketType $ticketType = null): Response
  {
    $data = [
      'ticketTypeId' => $ticketTypeId,
      'brandNewTicketType' => false,
    ];

    if ($ticketTypeId !== null) {
      if ($ticketType === null) {
        $ticketType = Eventsky::$plugin->ticketType->getTicketTypeById($ticketTypeId);

        if (!$ticketType) {
          throw new NotFoundHttpException('TicketType not found');
        }
      }

      $data['title'] = trim($ticketType->name) ?: Craft::t('eventsky', 'translate.ticketTypes.edit');
    } else {
      if ($ticketType === null) {
        $ticketType = new TicketType();
        $data['brandNewTicketType'] = true;
      }
      $data['title'] = Craft::t('eventsky', 'translate.ticketTypes.new');
    }

    $data['ticketType'] = $ticketType;

    $data['crumbs'] = [
      [
        'label' => Craft::t('eventsky', 'translate.ticketTypes.cpTitle'),
        'url' => UrlHelper::url('settings/sections')
      ],
    ];

    $data['tabs'] = [
      'settings' => [
        'label' => Craft::t('eventsky', 'translate.ticketType.tab.settings'),
        'url' => '#tickettype-settings'
      ],
      'fieldLayout' => [
        'label' => Craft::t('eventsky', 'translate.ticketType.tab.fieldlayout'),
        'url' => '#tickettype-fieldlayout'
      ]
    ];

    return $this->renderTemplate('eventsky/ticketTypes/edit', $data);
  }

  public function actionSave()
  {
    $this->requirePostRequest();

    $request = Craft::$app->getRequest();
    $ticketType = new TicketType();

    $ticketType->id = $request->getBodyParam('ticketTypeId');
    $ticketType->name = $request->getBodyParam('name');
    $ticketType->handle = $request->getBodyParam('handle');


    $fieldLayout = \Craft::$app->fields->assembleLayoutFromPost();
    $fieldLayout->type = Ticket::class;
    $ticketType->setFieldLayout($fieldLayout);

    Eventsky::$plugin->ticketType->saveTicketType($ticketType);
  }

  public function actionDelete(): Response
  {
    $this->requirePostRequest();
    $this->requireAcceptsJson();

    $ticketTypeId = Craft::$app->getRequest()->getRequiredBodyParam('id');
    Eventsky::$plugin->ticketType->deleteTicketTypeById($ticketTypeId);

    return $this->asJson(['success' => true]);
  }
}
