<?php
namespace fredmansky\eventsky\assetbundles;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class EventsAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@fredmansky/eventsky/resources/dist/";

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/events.css',
        ];

        $this->js = [
            'js/events.js',
        ];

        parent::init();
    }
}