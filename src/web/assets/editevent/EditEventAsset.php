<?php

namespace fredmansky\eventsky\web\assets\editevent;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class EditEventAsset extends AssetBundle
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
