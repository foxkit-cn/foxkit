<?php

namespace Foxkit\Info\Controller;

use Foxkit\Application as App;

/**
 * @Access(admin=true)
 */
class InfoController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Info'),
                'name'  => 'system:modules/info/views/info.php'
            ],
            '$info' => App::info()->get()
        ];
    }
}
