<?php

use Foxkit\Content\ContentHelper;
use Foxkit\Content\Plugin\MarkdownPlugin;
use Foxkit\Content\Plugin\SimplePlugin;
use Foxkit\Content\Plugin\VideoPlugin;

return [

    'name' => 'system/content',

    'main' => function ($app) {

        $app->subscribe(
            new MarkdownPlugin,
            new SimplePlugin,
            new VideoPlugin
        );

        $app['content'] = function() {
            return new ContentHelper;
        };

    },

    'autoload' => [

        'Pagekit\\Content\\' => 'src'

    ]

];
