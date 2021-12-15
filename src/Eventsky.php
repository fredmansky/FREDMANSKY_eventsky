<?php
/**
 * Eventsky plugin for Craft CMS 3.x
 *
 * Craft plugin for event management and attendee registration
 *
 * @link      https://fredmansky.at
 * @copyright Copyright (c) 2021 Fredmansky
 */

namespace fredmansky\eventsky;

use Craft;
use craft\base\Plugin;
use craft\events\PluginEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\App;
use craft\services\Elements;
use craft\services\Fields;
use craft\services\Plugins;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use fredmansky\eventsky\assetbundles\eventsky\EventskyAsset;
use fredmansky\eventsky\fields\EventskyField as EventskyFieldField;
use fredmansky\eventsky\models\Settings;
use fredmansky\eventsky\services\EventskyService as EventskyServiceService;
use fredmanskyeventsky\eventsky\variables\EventskyVariable;
use nystudio107\pluginvite\services\VitePluginService;
use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Fredmansky
 * @package   Eventsky
 * @since     1.0.0
 *
 * @property  Settings $settings
 * @method    Settings getSettings()
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
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = true;

    // Public Methods
    // =========================================================================

    public function __construct($id, $parent = null, array $config = [])
    {
        $config['components'] = [
            'eventsky' => Eventsky::class,
            'vite' => [
                'class' => VitePluginService::class,
                'assetClass' => EventskyAsset::class,
                'useDevServer' => true,
                'devServerPublic' => 'http://localhost:3001',
                'serverPublic' => App::env('PRIMARY_SITE_URL'),
                'errorEntry' => 'src/js/app.ts',
                'devServerInternal' => 'http://localhost:3001',
                'checkDevServer' => true,
            ]
        ];

        parent::__construct($id, $parent, $config);
    }

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

        $this->initEventskyVariable();
        $this->registerSiteRoutes();
        $this->registerCPRoutes();
        $this->registerFields();
        $this->initLogging();
    }

    // Protected Methods
    // =========================================================================


    // Private Methods
    // =========================================================================

    private function initEventskyVariable(): void
    {
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function(Event $event) {
                $variable = $event->sender;
                $variable->set('eventsky', [
                    'class' => EventskyVariable::class,
                    'viteService' => $this->vite,
                ]);
            }
        );
    }

    private function registerSiteRoutes()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = array_merge(
                    $event->rules,
                    include __DIR__ . '/siteRoutes.php',
                );
            }
        );
    }

    private function registerCPRoutes()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules = array_merge(
                    $event->rules,
                    include __DIR__ . '/cpRoutes.php',
                );
            }
        );
    }

    private function registerFields()
    {
//        Event::on(
//            Fields::class,
//            Fields::EVENT_REGISTER_FIELD_TYPES,
//            function (RegisterComponentTypesEvent $event) {
//                $event->types[] = EventskyFieldField::class;
//            }
//        );
    }

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
    private function initLogging()
    {
        Craft::info(
            Craft::t(
                'eventsky',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }
}
