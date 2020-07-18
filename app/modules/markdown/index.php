<?php

use Foxkit\Markdown\Markdown;

return [

    'name' => 'markdown',

    'main' => function ($app) {

        $app['markdown'] = function() {
            return new Markdown;
        };

    },

    'autoload' => [

        'Foxkit\\Markdown\\' => 'src'

    ]

];
