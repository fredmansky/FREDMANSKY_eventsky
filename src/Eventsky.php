<?php
/**
 * Event plugin for Craft CMS 3.x
 *
 * Craft plugin for event management and attendee registration
 *
 * @link      http://fredmansky.at
 * @copyright Copyright (c) 2019 Fredmansky GmbH
 */

namespace fredmansky\eventsky;


use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use fredmansky\eventsky\fields\EventField;
use fredmansky\eventsky\services\EmailNotificationService;
use fredmansky\eventsky\services\EventService;
use fredmansky\eventsky\services\EventTypeService;
use fredmansky\eventsky\services\FieldService;
use fredmansky\eventsky\services\MailService;
use fredmansky\eventsky\services\TicketService;
use fredmansky\eventsky\services\TicketStatusService;
use fredmansky\eventsky\services\TicketTypeService;
use fredmansky\eventsky\services\TwigTemplateService;
use yii\base\Event;

/**
 * Class Eventsky
 * @package fredmansky\eventsky
 *
 * @property EventService $event
 * @property EventTypeService $eventType
 * @property TicketService $ticket
 * @property TicketTypeService $ticketType
 * @property TicketStatusService $ticketStatus
 * @property FieldService $fieldService
 * @property EmailNotificationService $emailNotification
 * @property MailService $mail
 */
class Eventsky extends Plugin
{
    public static $plugin;

    public string $schemaVersion = '1.9.5';

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->hasCpSettings = true;
        $this->hasCpSection = true;

        $this->installEventListeners();
        $this->installTwigExtensions();

        $this->setComponents([
            'event' => EventService::class,
            'eventType' => EventTypeService::class,
            'ticket' => TicketService::class,
            'ticketType' => TicketTypeService::class,
            'ticketStatus' => TicketStatusService::class,
            'fieldService' => FieldService::class,
            'emailNotification' => EmailNotificationService::class,
            'mail' => MailService::class,
        ]);
    }

    public function getCpNavItem(): ?array
    {
        $item = parent::getCpNavItem();
        $item['label'] = 'Eventsky';
        // $item['badgeCount'] = 5;
        $item['subnav'] = [
            'events' => ['label' => Craft::t('eventsky', 'translate.events.cpTitle'), 'url' => 'eventsky/events'],
            'tickets' => ['label' => Craft::t('eventsky', 'translate.tickets.cpTitle'), 'url' => 'eventsky/tickets'],
            'eventTypes' => ['label' => Craft::t('eventsky', 'translate.eventTypes.cpTitle'), 'url' => 'eventsky/eventtypes'],
            'ticketTypes' => ['label' => Craft::t('eventsky', 'translate.ticketTypes.cpTitle'), 'url' => 'eventsky/tickettypes'],
            'emailNotifications' => ['label' => Craft::t('eventsky', 'translate.emailNotifications.cpTitle'), 'url' => 'eventsky/emailnotifications'],
            'settings' => ['label' => Craft::t('eventsky', 'translate.settings.cpTitle'), 'url' => 'eventsky/settings'],
        ];
        return $item;
    }

    protected function createSettingsModel(): ?\craft\base\Model
    {
        return new \fredmansky\eventsky\models\Settings();
    }

    protected function settingsHtml(): ?string
    {
        return \Craft::$app->getView()->renderTemplate('eventsky/settings', [
            'settings' => $this->getSettings()
        ]);
    }

    protected function installEventListeners()
    {
      $request = Craft::$app->getRequest();
      $this->installCpEventListeners();
      $this->installSiteEventListeners();
      $this->installFieldTypesEventListener();
    }

    protected function installCpEventListeners()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                Craft::debug(
                    'UrlManager::EVENT_REGISTER_CP_URL_RULES',
                    __METHOD__
                );
                // Register our Control Panel routes
                $event->rules = array_merge(
                    $event->rules,
                    $this->customAdminCpRoutes()
                );
            }
        );
    }

    protected function installSiteEventListeners()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function(RegisterUrlRulesEvent $event) {
                // Register our Site routes
                $event->rules = array_merge(
                    $event->rules,
                    $this->customSiteRoutes()
                );
            }
        );
    }

    protected function installFieldTypesEventListener()
    {
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = EventField::class;
        });
    }

    protected function installTwigExtensions()
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $e) {
            /** @var CraftVariable $variable */
            $variable = $e->sender;

            // Attach a service:
            $variable->set('eventsky', TwigTemplateService::class);
        });
    }

    protected function customAdminCpRoutes(): array
    {
        return include __DIR__ . '/cpRoutes.php';
    }

    protected function customSiteRoutes(): array
    {
        return include __DIR__ . '/siteRoutes.php';
    }
}
