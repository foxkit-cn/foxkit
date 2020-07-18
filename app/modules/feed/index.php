<?php

use Foxkit\Feed\FeedFactory;

return [

    'name' => 'feed',

    'main' => function ($app) {

        $app['feed'] = function () {
            return new FeedFactory;
        };

    },

    'autoload' => [

        'Foxkit\\Feed\\' => 'src'

    ]

];
