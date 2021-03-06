<?php

use Foxkit\Filesystem\Adapter\FileAdapter;
use Foxkit\Filesystem\Filesystem;
use Foxkit\Filesystem\Locator;
use Foxkit\Filesystem\StreamWrapper;

return [

    'name' => 'filesystem',

    'main' => function ($app) {

        $app['file'] = function () {
            return new Filesystem;
        };

        $app['locator'] = function () {
            return new Locator($this->config['path']);
        };

        $app['module']->addLoader(function ($module) use ($app) {

            if (isset($module['resources'])) {
                foreach ($module['resources'] as $prefix => $path) {
                    $app['locator']->add($prefix, "{$module['path']}/$path");
                }
            }

            return $module;
        });

    },

    'events' => [

        'boot' => function ($event, $app) {

            StreamWrapper::setFilesystem($app['file']);

        },

        'request' => [function ($event, $request) use ($app) {

            $baseUrl = $request->getSchemeAndHttpHost().$request->getBasePath();

            $app['file']->registerAdapter('file', new FileAdapter($this->config['path'], $baseUrl));

        }, 100]
    ],

    'autoload' => [

        'Foxkit\\Filesystem\\' => 'src'

    ],

    'config' => [

        'path' => getcwd()

    ]

];
