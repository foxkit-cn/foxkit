<?php

$autoload = [
    'Foxkit\\Auth\\' => '/app/modules/auth/src',
    'Foxkit\\Config\\' => '/app/modules/config/src',
    'Foxkit\\Cookie\\' => '/app/modules/cookie/src',
    'Foxkit\\Database\\' => '/app/modules/database/src',
    'Foxkit\\Filesystem\\' => '/app/modules/filesystem/src',
    'Foxkit\\Filter\\' => '/app/modules/filter/src',
    'Foxkit\\Migration\\' => '/app/modules/migration/src',
    'Foxkit\\Package\\' => '/app/modules/package/src',
    'Foxkit\\Routing\\' => '/app/modules/routing/src',
    'Foxkit\\Session\\' => '/app/modules/session/src',
    'Foxkit\\Tree\\' => '/app/modules/tree/src',
    'Foxkit\\View\\' => '/app/modules/view/src'
];

$path = realpath(__DIR__.'/../../../../../');
$loader = require $path.'/autoload.php';

foreach ($autoload as $namespace => $src) {
    $loader->addPsr4($namespace, $path.$src);
}
