<?php

namespace fredmansky\eventsky\models;

use craft\base\Model;

class Settings extends Model
{
    public $url = 'https://fredmansky.at/';

    public function rules()
    {
        return [
            [['url'], 'required'],
            // ...
        ];
    }
}