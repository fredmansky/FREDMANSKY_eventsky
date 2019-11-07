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
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use fredmansky\eventsky\services\EventService;
use fredmansky\eventsky\services\EventTypeService;
use fredmansky\eventsky\services\TicketService;
use fredmansky\eventsky\services\TicketStatusService;
use fredmansky\eventsky\services\TicketTypeService;
use fredmansky\vidsky\services\Video;
use yii\base\Event;

class Eventsky extends Plugin
{
    public static $plugin;

    public $schemaVersion = '0.0.1';

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->hasCpSettings = true;
        $this->hasCpSection = true;

        $this->installEventListeners();
        $this->setComponents([
            'event' => EventService::class,
            'eventType' => EventTypeService::class,
            'ticket' => TicketService::class,
            'ticketType' => TicketTypeService::class,
            'ticketStatus' => TicketStatusService::class,
        ]);
    }

    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['label'] = 'Eventsky';
        // $item['badgeCount'] = 5;
        $item['subnav'] = [
            'events' => ['label' => Craft::t('eventsky', 'translate.events.cpTitle'), 'url' => 'eventsky/events'],
            'tickets' => ['label' => Craft::t('eventsky', 'translate.tickets.cpTitle'), 'url' => 'eventsky/tickets'],
            'eventTypes' => ['label' => Craft::t('eventsky', 'translate.eventTypes.cpTitle'), 'url' => 'eventsky/eventtypes'],
            'ticketTypes' => ['label' => Craft::t('eventsky', 'translate.ticketTypes.cpTitle'), 'url' => 'eventsky/tickettypes'],
            'settings' => ['label' => Craft::t('eventsky', 'translate.settings.cpTitle'), 'url' => 'eventsky/settings'],
        ];
        return $item;
    }

    protected function createSettingsModel()
    {
        return new \fredmansky\eventsky\models\Settings();
    }

    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate('eventsky/settings', [
            'settings' => $this->getSettings()
        ]);
    }


    protected function installEventListeners()
    {
      $request = Craft::$app->getRequest();
      $this->installCpEventListeners();
    }

    protected function installCpEventListeners()
    {
        // Handler: UrlManager::EVENT_REGISTER_CP_URL_RULES
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

    protected function customAdminCpRoutes(): array
    {
        return include __DIR__ . '/cpRoutes.php';
    }
}
