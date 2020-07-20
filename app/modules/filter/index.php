<?php

use Foxkit\Filter\FilterManager;

return [

    'name' => 'filter',

    'main' => function ($app) {

        $app['filter'] = function() {
            return new FilterManager($this->config['defaults']);
        };

    },

    'autoload' => [

        'Foxkit\\Filter\\' => 'src'

    ],

    'config' => [

        'defaults' => null

    ]
];
