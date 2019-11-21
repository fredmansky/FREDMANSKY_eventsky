<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\controllers;

use Craft;

use craft\models\FieldLayout;
use fredmansky\eventsky\elements\Ticket;
use fredmansky\eventsky\elements\db\TicketTypeQuery;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\models\TicketType;
use craft\helpers\UrlHelper;
use craft\web\Controller;

use yii\web\HttpException;
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

  /**
   * @param int|null $ticketTypeId
   * @param TicketType|null $ticketType The ticketType being edited, if there were any validation errors.
   * @return Response
   * @throws NotFoundHttpException
   */
    public function actionEdit(int $ticketTypeId = null, TicketType $ticketType = null): Response
    {
        $data = [
            'ticketTypeId' => $ticketTypeId,
            'brandNewTicketType' => false,
        ];

        if ($ticketType) {
          if ($ticketType->fieldLayoutId) {
            $fieldlayout = Craft::$app->fields->getLayoutById($ticketType->fieldLayoutId);

            if (!$fieldlayout) {
              throw new NotFoundHttpException(Craft::t('eventsky', 'translate.fieldlayout.notFound'));
            }

            $data['title'] = trim($ticketType->name) ?: Craft::t('eventsky', 'translate.ticketTypes.edit');
            $data['fieldlayout'] = $fieldlayout;
          }
          else {
            $data['brandNewTicketType'] = true;
            $data['title'] = Craft::t('eventsky', 'translate.ticketTypes.new');
            $data['fieldlayout'] = new FieldLayout();
          }
        }
        else if ($ticketTypeId !== null) {
            $ticketType = Eventsky::$plugin->ticketType->getTicketTypeById($ticketTypeId);

            if (!$ticketType) {
                throw new NotFoundHttpException(Craft::t('eventsky', 'translate.ticketTypes.notFound'));
            }

            $data['title'] = trim($ticketType->name) ?: Craft::t('eventsky', 'translate.ticketTypes.edit');
            $fieldlayout = Craft::$app->fields->getLayoutById($ticketType->fieldLayoutId);

            if (!$fieldlayout) {
                throw new NotFoundHttpException(Craft::t('eventsky', 'translate.fieldlayout.notFound'));
            }

            $data['fieldlayout'] = $fieldlayout;
        } else {
            $ticketType = new TicketType();
            $data['brandNewTicketType'] = true;
            $data['title'] = Craft::t('eventsky', 'translate.ticketTypes.new');
            $data['fieldlayout'] = new FieldLayout();
        }

        $data['ticketType'] = $ticketType;

        $data['crumbs'] = [
            [
                'label' => Craft::t('eventsky', 'translate.ticketTypes.cpTitle'),
                'url' => UrlHelper::url('eventsky/tickettypes')
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
        $ticketTypeId = $request->getBodyParam('ticketTypeId');

        if ($ticketTypeId) {
            $ticketType = Eventsky::$plugin->ticketType->getTicketTypeById($ticketTypeId);

            if (!$ticketType) {
                throw new HttpException(404, Craft::t('eventsky', 'translate.ticketType.notFound'));
            }
        } else {
            $ticketType = new TicketType();
        }

        $ticketType->id = $request->getBodyParam('ticketTypeId');
        $ticketType->name = $request->getBodyParam('name');
        $ticketType->handle = $request->getBodyParam('handle');

        $fieldLayout = \Craft::$app->fields->assembleLayoutFromPost();
        $fieldLayout->type = Ticket::class;
        $ticketType->setFieldLayout($fieldLayout);

        if (!Eventsky::$plugin->ticketType->saveTicketType($ticketType)) {
            Craft::$app->getSession()->setError(Craft::t('eventsky', 'translate.ticketTypes.save.error'));

            // Send the event type back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'ticketType' => $ticketType,
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('eventsky', 'translate.ticketTypes.save.success'));
        return $this->redirectToPostedUrl($ticketType);
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
