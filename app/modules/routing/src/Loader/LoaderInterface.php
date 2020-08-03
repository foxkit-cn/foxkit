<?php

namespace Foxkit\Routing\Loader;

use Foxkit\Routing\Route;

interface LoaderInterface
{
    /**
     * 加载路由
     *
     * @param mixed $routes
     * @return Route[]
     */
    public function load($routes);
}
