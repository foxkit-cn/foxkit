<?php

return [

    'name' => 'system/intl',

    'main' => 'Foxkit\\Intl\\IntlModule',

    'autoload' => [

        'Foxkit\\Intl\\' => 'src'

    ],

    'resources' => [

        'system/intl:' => ''

    ],

    'routes' => [
        '/system/intl' => [
            'name' => '@system/intl',
            'controller' => 'Foxkit\\Intl\\Controller\\IntlController'
        ],
    ],

    'config' => [

        'locale' => 'en_US'

    ],

    'events' => [

        'view.init' => function ($event, $view) {
            $view->addGlobal('intl', $this);
        }

    ]

];
