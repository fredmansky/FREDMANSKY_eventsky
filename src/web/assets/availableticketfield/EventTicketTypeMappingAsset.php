<?php

namespace fredmansky\eventsky\web\assets\availableticketfield;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class EventTicketTypeMappingAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = __DIR__ . '/';

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'style.css',
        ];

        $this->js = [
            'index.js',
        ];

        parent::init();
    }
}
