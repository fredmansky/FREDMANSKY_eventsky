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
use craft\services\Plugins;
use craft\events\PluginEvent;

use craft\events\RegisterCpNavItemsEvent;
use craft\web\twig\variables\Cp;

use craft\web\UrlManager;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Fredmansky
 * @package   Eventsky
 * @since     0.0.1
 *
 *
 * @property mixed $cpNavItem
 */
class Eventsky extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Eventsky::$plugin
     *
     * @var Eventsky
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '0.0.1';

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Eventsky::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->hasCpSettings = true;
        $this->hasCpSection = true;

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(
            Craft::t(
                'eventsky',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['label'] = 'Eventsky';
        // $item['badgeCount'] = 5;
        $item['subnav'] = [
            'events' => ['label' => Craft::t('eventsky', 'translate.events.cpTitle'), 'url' => 'eventsky/events'],
            'tickets' => ['label' => Craft::t('eventsky', 'translate.tickets.cpTitle'), 'url' => 'eventsky/tickets'],
            'eventTypes' => ['label' => Craft::t('eventsky', 'translate.eventTypes.cpTitle'), 'url' => 'eventsky/eventTypes'],
            'ticketTypes' => ['label' => Craft::t('eventsky', 'translate.ticketTypes.cpTitle'), 'url' => 'eventsky/ticketTypes'],
            'settings' => ['label' => Craft::t('eventsky', 'translate.settings.cpTitle'), 'url' => 'eventsky/settings'],
        ];
        return $item;
    }
    
    // Protected Methods
    // =========================================================================

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

    // Private Methods
    // =========================================================================

    private function initRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $routes = include 'routes.php';
                $event->rules = array_merge($event->rules, $routes);
            }
        );
    }
}
