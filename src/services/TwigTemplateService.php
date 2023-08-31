<?php

namespace fredmansky\eventsky\services;

use craft\base\Component;
use craft\elements\db\ElementQueryInterface;
use fredmansky\eventsky\elements\Event;

class TwigTemplateService extends Component
{
    public function init(): void
    {
        parent::init();
    }

    public function events(): ElementQueryInterface
    {
        // return EventQuery
        return Event::find();
    }
}
