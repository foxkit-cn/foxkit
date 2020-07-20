<?php

namespace Foxkit\Routing\Loader;

use Foxkit\Routing\Route;

interface LoaderInterface
{
    /**
     * Loads routes.
     *
     * @param  mixed $routes
     * @return Route[]
     */
    public function load($routes);
}
