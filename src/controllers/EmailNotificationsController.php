<?php
/**
 * @link https://fredmansky.at/
 * @copyright Copyright (c) Fredmansky GmbH
 */

namespace fredmansky\eventsky\controllers;

use Craft;

use craft\helpers\UrlHelper;
use fredmansky\eventsky\Eventsky;
use craft\web\Controller;

use fredmansky\eventsky\models\EmailNotification;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class EmailNotificationsController extends Controller
{
    public function actionIndex(array $variables = []): Response
    {
        $data = [
            'emailNotifications' => Eventsky::$plugin->emailNotification->getAllEmailNotifications(),
        ];

        return $this->renderTemplate('eventsky/emailNotifications/index', $data);
    }

    public function actionEdit(int $emailNotificationId = null): Response
    {
        $data = [
            'emailNotificationId' => $emailNotificationId,
            'brandNewEmailNotification' => false,
        ];

        if ($emailNotificationId !== null) {
            $emailNotification = Eventsky::$plugin->emailNotification->getEmailNotificationById($emailNotificationId);

            if (!$emailNotification) {
                throw new NotFoundHttpException(Craft::t('eventsky', 'translate.emailNotifications.notFound'));
            }

            $data['title'] = trim($emailNotification->name) ?: Craft::t('eventsky', 'translate.emailNotifications.edit');
        } else {
            $emailNotification = new EmailNotification();
            $data['brandNewEmailNotification'] = true;
            $data['title'] = Craft::t('eventsky', 'translate.emailNotifications.new');
        }

        $data['emailNotification'] = $emailNotification;

        $data['crumbs'] = [
            [
                'label' => Craft::t('eventsky', 'translate.emailNotifications.cpTitle'),
                'url' => UrlHelper::url('eventsky/emailnotifications')
            ],
        ];

        return $this->renderTemplate('eventsky/emailnotifications/edit', $data);
    }

    public function actionSave(): Response
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $emailNotificationId = $request->getBodyParam('emailNotificationId');

        if ($emailNotificationId) {
            $emailNotification = Eventsky::$plugin->emailNotification->getEmailNotificationById($emailNotificationId);

            if (!$emailNotification) {
                throw new HttpException(404, Craft::t('eventsky', 'translate.emailNotifications.notFound'));
            }
        } else {
            $emailNotification = new EmailNotification();
        }

        $emailNotification->name = $request->getBodyParam('name');
        $emailNotification->handle = $request->getBodyParam('handle');
        $emailNotification->subject = $request->getBodyParam('subject');
        $emailNotification->fromEmail = $request->getBodyParam('fromEmail');
        $emailNotification->replyToEmail = $request->getBodyParam('replyToEmail');
        $emailNotification->textContent = $request->getBodyParam('textContent');

        if (!Eventsky::$plugin->emailNotification->saveEmailNotification($emailNotification)) {
            Craft::$app->getSession()->setError(Craft::t('eventsky', 'translate.emailNotifications.save.error'));

            // Send the event type back to the template
            Craft::$app->getUrlManager()->setRouteParams([
                'emailNotification' => $emailNotification,
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('eventsky', 'translate.emailNotifications.save.success'));
        return $this->redirectToPostedUrl($emailNotification);
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $emailNotificationId = Craft::$app->getRequest()->getRequiredBodyParam('id');
        Eventsky::$plugin->emailNotification->deleteEmailNotificationById($emailNotificationId);

        return $this->asJson(['success' => true]);
    }
}
