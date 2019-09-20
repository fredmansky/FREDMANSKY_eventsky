<?php

namespace fredmansky\eventsky;

use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\models\TicketType;

use Craft;
use craft\web\Controller;

use yii\base\Exception;
use yii\web\Response;

class TicketTypesController extends Controller
{
  // Public Methods
  // =========================================================================

  public function init()
  {
    // $this->requirePermission('events-manageTicketTypes');

    parent::init();
  }

  /**
   * Ticket types index.
   *
   * @param array $variables
   * @return Response The rendering result
   */
  public function actionIndex(array $variables = []): Response
  {
    $variables['tickettypes'] = Craft::$app->getSections()->getAllSections();
    // VarDumper::dump($variables, $depth = 20, $highlight = true);
    return $this->renderTemplate('eventsky/ticketTypes/index', $variables);
  }

  public function actionEdit(int $ticketTypeId = null, TicketType $ticketType = null): Response
  {
    $variables = [
      'ticketTypeId' => $ticketTypeId,
      'ticketType' => $ticketType,
      'newTicketType' => false
    ];

    if (empty($variables['ticketType'])) {
      if ($ticketType !== null) {
        $ticketType = TicketType::find()->id($ticketTypeId);
        if (!$ticketType) {
          throw new NotFoundHttpException('Ticket Type not found');
        }
        $variables['ticketType'] = $ticketType;
        if ($ticketType->title !== null) {
          $variables['title'] = trim($ticketType->title) ?: Craft::t('app', 'Edit Ticket Type');
        }
      } else {
        $variables['ticketType'] = new TicketType();
        $variables['newTicketType'] = true;
        $variables['title'] = Craft::t('app', 'Create a new Ticket Type');
      }
    }

    $variables['crumbs'] = [
      [
        'label' => Craft::t('app', 'Eventsky'),
        'url' => UrlHelper::url('eventsky')
      ],
      [
        'label' => Craft::t('app', 'Ticket Types'),
        'url' => UrlHelper::url('eventsky/ticketTypes')
      ],
    ];
    return $this->renderTemplate('eventsky/ticketTypes/edit', $variables);
  }

  public function actionSave()
  {
    $this->requirePostRequest();

    $ticketType = new TicketType();
    $request = Craft::$app->getRequest();
    $ticketType->id = $request->getBodyParam('eventTypeId');
    $ticketType->title = $request->getBodyParam('title');

    // Save it
    // TODO: Event Type Service to save Event Type
    // Craft::$app->getSession()->setNotice(Craft::t('eventsky', 'Ticket type saved.'));
    // return $this->redirectToPostedUrl($ticketType);
  }

  /**
   * @return Response
   * @throws Exception
   * @throws \Throwable
   * @throws \craft\errors\MissingComponentException
   * @throws \yii\web\BadRequestHttpException
   */
  public function actionDelete(): Response
  {
    $this->requirePostRequest();
    $this->requireAcceptsJson();

    $ticketTypeId = Craft::$app->getRequest()->getRequiredParam('id');
    $ticketType = TicketType::find()->id($ticketTypeId);

    if (!$ticketType) {
      throw new Exception(Craft::t('eventsky', 'No ticket type exists with the ID “{id}”.', ['id' => $ticketTypeId]));
    }

    if (!Craft::$app->getElements()->deleteElement($ticketType)) {
      if (Craft::$app->getRequest()->getAcceptsJson()) {
        $this->asJson(['success' => false]);
      }

      Craft::$app->getSession()->setError(Craft::t('eventsky', 'Couldn’t delete ticket type.'));
      Craft::$app->getUrlManager()->setRouteParams([
        'ticketType' => $ticketType,
      ]);

      return null;
    }

    if (Craft::$app->getRequest()->getAcceptsJson()) {
      return $this->asJson(['success' => true]);
    }

    Craft::$app->getSession()->setNotice(Craft::t('eventsky', 'Ticket Type deleted.'));

    return $this->redirectToPostedUrl($ticketType);
  }

}
