<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\helpers\Db;
use craft\helpers\StringHelper;
use craft\services\Fields;
use fredmansky\eventsky\db\Table;
use fredmansky\eventsky\models\EmailNotification;
use fredmansky\eventsky\records\EmailNotificationRecord;
use yii\db\ActiveQuery;

class EmailNotificationService extends Fields
{
    private $emailNotifications;

    public function getAllEmailNotifications(): array
    {
        if ($this->emailNotifications !== null) {
            return $this->emailNotifications;
        }

        $results = $this->createEmailNotificationQuery()
            ->all();

        $this->emailNotifications = array_map(function($result) {
            return new EmailNotification($result);
        }, $results);
        return $this->emailNotifications;
    }

    public function getEmailNotificationById(int $id): ?EmailNotification
    {
        $result = $this->createEmailNotificationQuery()
            ->where(['=', 'id', $id])
            ->one();

        if ($result) {
            return new EmailNotification($result);
        }

        return null;
    }

    public function saveEmailNotification(EmailNotification $emailNotification, bool $runValidation = true)
    {
        $isNewEmailNotification = !$emailNotification->id;

        if ($runValidation && !$emailNotification->validate()) {
            \Craft::info('Email notification not saved due to validation error.', __METHOD__);
            return false;
        }

        if ($isNewEmailNotification) {
            $emailNotification->uid = StringHelper::UUID();
        } else if (!$emailNotification->uid) {
            $emailNotification->uid = Db::uidById(Table::EMAIL_NOTIFICATIONS, $emailNotification->id);
        }

        $emailNotificationRecord = EmailNotificationRecord::find()
            ->where(['=', 'id', $emailNotification->id])
            ->one();

        if (!$emailNotificationRecord) {
            $emailNotificationRecord = new EmailNotificationRecord();
        }

        $emailNotificationRecord->name = $emailNotification->name;
        $emailNotificationRecord->handle = $emailNotification->handle;
        $emailNotificationRecord->subject = $emailNotification->subject;
        $emailNotificationRecord->fromEmail = $emailNotification->fromEmail;
        $emailNotificationRecord->replyToEmail = $emailNotification->replyToEmail;
        $emailNotificationRecord->textContent = $emailNotification->textContent;
        $emailNotificationRecord->uid = $emailNotification->uid;
        $emailNotificationRecord->save();

        // @TODO add exceptions when saving is failing
        return true;
    }

    public function deleteEmailNotificationById(int $id): bool
    {
        $emailNotification = $this->getEmailNotificationById($id);

        if (!$emailNotification) {
            return false;
        }

        return $this->deleteEmailNotification($emailNotification);
    }

    public function deleteEmailNotification(EmailNotification $emailNotification): bool
    {
        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            Craft::$app->getDb()->createCommand()
                ->delete(Table::EMAIL_NOTIFICATIONS, ['id' => $emailNotification->id])
                ->execute();

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        // Clear caches
        $this->emailNotifications = null;

        return true;
    }

    private function createEmailNotificationQuery(): ActiveQuery
    {
        return EmailNotificationRecord::find()
            ->orderBy(['name' => SORT_ASC]);
    }
}
