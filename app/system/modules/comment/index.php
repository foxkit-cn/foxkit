<?php

use Foxkit\Comment\CommentPlugin;

return [

    'name' => 'system/comment',

    'main' => function ($app) {

        $app->subscribe(new CommentPlugin);

    },

    'autoload' => [

        'Foxkit\\Comment\\' => 'src'

    ]

];
