<?php

use Foxkit\User\Event\AccessListener;
use Foxkit\User\Event\AuthorizationListener;
use Foxkit\User\Event\LoginAttemptListener;
use Foxkit\User\Event\UserListener;

return [

    'name' => 'system/user',

    'main' => 'Foxkit\\User\\UserModule',

    'autoload' => [

        'Foxkit\\User\\' => 'src'

    ],

    'routes' => [

        '/user' => [
            'name' => '@user',
            'controller' => [
                'Foxkit\\User\\Controller\\AuthController',
                'Foxkit\\User\\Controller\\UserController'
            ]
        ],
        '/user/profile' => [
            'name' => '@user/profile',
            'controller' => [
                'Foxkit\\User\\Controller\\ProfileController',
            ]
        ],
        '/user/registration' => [
            'name' => '@user/registration',
            'controller' => [
                'Foxkit\\User\\Controller\\RegistrationController',
            ]
        ],
        '/user/resetpassword' => [
            'name' => '@user/resetpassword',
            'controller' => [
                'Foxkit\\User\\Controller\\ResetPasswordController',
            ]
        ],
        '/api/user' => [
            'name' => '@user/api',
            'controller' => [
                'Foxkit\\User\\Controller\\UserApiController'
            ]
        ],
        '/api/user/role' => [
            'name' => '@user/api/role',
            'controller' => [
                'Foxkit\\User\\Controller\\RoleApiController'
            ]
        ]

    ],

    'widgets' => [

        'widgets/login.php'

    ],

    'resources' => [

        'system/user:' => '',
        'views:system/user' => 'views'

    ],

    'permissions' => [

        'user: manage users' => [
            'title' => 'Manage users',
            'trusted' => true
        ],
        'user: manage user permissions' => [
            'title' => 'Manage user permissions',
            'trusted' => true
        ],
        'system: access admin area' => [
            'title' => 'Access admin area',
            'description' => 'Allows to access the admin area and to use the site in maintenance mode',
            'trusted' => true
        ]

    ],

    'menu' => [

        'user' => [
            'label' => 'Users',
            'icon' => 'system/user:assets/images/icon-users.svg',
            'url' => '@user',
            'active' => '@user(/*)?',
            'access' => 'user: manage users || user: manage user permissions || system: access settings',
            'priority' => 115
        ],
        'user: users' => [
            'label' => 'List',
            'parent' => 'user',
            'url' => '@user',
            'active' => '@user(/edit)?',
            'access' => 'user: manage users',
        ],
        'user: permissions' => [
            'label' => 'Permissions',
            'parent' => 'user',
            'url' => '@user/permissions',
            'access' => 'user: manage user permissions'
        ],
        'user: roles' => [
            'label' => 'Roles',
            'parent' => 'user',
            'url' => '@user/roles',
            'access' => 'user: manage user permissions'
        ],
        'user: settings' => [
            'label' => 'Settings',
            'parent' => 'user',
            'url' => '@user/settings',
            'access' => 'system: access settings'
        ]

    ],

    'config' => [

        'registration' => 'admin',
        'require_verification' => true,
        'users_per_page' => 20,
        'login_redirect' => '', // use route syntax, i.e. @page/1. empty string for index

        'auth' => [
            'refresh_token' => false
        ]

    ],

    'events' => [

        'boot' => function ($event, $app) {
            $app->subscribe(
                new AccessListener,
                new AuthorizationListener,
                new LoginAttemptListener,
                new UserListener
            );
        },

        'view.scripts' => function ($event, $scripts) use ($app) {
            if ($app['user']->hasAccess('user: manage users')) {
                $scripts->register('widget-user', 'system/user:app/bundle/widget-user.js', '~dashboard');
            }
            $scripts->register('link-user', 'system/user:app/bundle/link-user.js', '~panel-link');

            if ($app['user']->isAuthenticated()) {
                $scripts->register('auth', 'system/user:app/bundle/interceptor.js', ['~vue']);
            }
        }

    ]

];
