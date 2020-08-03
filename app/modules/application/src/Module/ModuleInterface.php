<?php

namespace Foxkit\Module;

use Foxkit\Application as App;

interface ModuleInterface
{
    /**
     * 主要的引导方法
     *
     * @param App $app
     */
    public function main(App $app);

    /**
     * 获取一个选项值
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * 获取一个配置值
     *
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function config($key = null, $default = null);
}
