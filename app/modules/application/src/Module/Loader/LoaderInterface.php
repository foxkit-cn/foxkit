<?php

namespace Foxkit\Module\Loader;

interface LoaderInterface
{
    /**
     * 加载模块
     *
     * @param mixed $module
     * @return mixed
     */
    public function load($module);
}
