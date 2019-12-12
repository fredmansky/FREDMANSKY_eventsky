<?php

namespace fredmansky\eventsky\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\helpers\ArrayHelper;
use craft\services\Fields;
use fredmansky\eventsky\elements\Event;
use fredmansky\eventsky\Eventsky;
use fredmansky\eventsky\fields\EventTicketTypeMappingField;

class MailService extends Fields
{
    public function sendMail(string $from, string $to, string $replyTo, string $subject, string $body): bool
    {
        // Docs: https://www.yiiframework.com/doc/api/2.0/yii-mail-messageinterface
        return Craft::$app
            ->getMailer()
            ->compose()
            ->setFrom($from)
            ->setTo($to)
            ->setReplyTo($replyTo)
            ->setSubject($subject)
            ->setTextBody($body)
            ->send();
    }
}
