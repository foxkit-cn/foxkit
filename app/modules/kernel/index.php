<?php

use Foxkit\Kernel\Controller\ControllerListener;
use Foxkit\Kernel\Controller\ControllerResolver;
use Foxkit\Kernel\Event\JsonResponseListener;
use Foxkit\Kernel\Event\ResponseListener;
use Foxkit\Kernel\Event\StringResponseListener;
use Foxkit\Kernel\HttpKernel;
use Symfony\Component\HttpFoundation\RequestStack;

return [

    'name' => 'kernel',

    'main' => function ($app) {

        $app['kernel'] = function ($app) {

            $app->subscribe(
                new ControllerListener($app['resolver']),
                new ResponseListener(),
                new JsonResponseListener(),
                new StringResponseListener()
            );

            return new HttpKernel($app['events'], $app['request.stack']);
        };

        $app['resolver'] = function () {
            return new ControllerResolver();
        };

        $app->factory('request', function ($app) {
            return $app['request.stack']->getCurrentRequest();
        });

        $app['request.stack'] = function () {
            return new RequestStack();
        };

    },

    'events' => [

        'request' => [function ($event, $request) use ($app) {

            if ($app->inConsole()) {
                return;
            }

            $path = $request->getPathInfo();

            // redirect the request if it has a trailing slash
            if ('/' != $path && '/' == substr($path, -1) && '//' != substr($path, -2)) {
                $event->setResponse($app->redirect(rtrim($request->getUriForPath($path), '/'), [], 301));
            }

        }, 200]

    ],

    'autoload' => [

        'Foxkit\\Kernel\\' => 'src'

    ]

];
