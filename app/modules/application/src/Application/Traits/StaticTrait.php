<?php

namespace Foxkit\Application\Traits;

use Foxkit\Container;

trait StaticTrait
{
    protected static $instance;

    /**
     * 获取一个容器实例
     *
     * @return Container
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * 检查是否定义了参数或服务
     *
     * @param string $name
     * @return bool
     */
    public static function has($name)
    {
        return static::$instance->offsetExists($name);
    }

    /**
     * 获取一个参数或服务
     *
     * @param string $name
     * @return mixed
     */
    public static function get($name)
    {
        return static::$instance->offsetGet($name);
    }

    /**
     * 设置一个参数或服务
     *
     * @param string $name
     * @param mixed $value
     */
    public static function set($name, $value)
    {
        static::$instance->offsetSet($name, $value);
    }

    /**
     * 删除一个参数或服务
     *
     * @param string $name
     */
    public static function remove($name)
    {
        static::$instance->offsetUnset($name);
    }

    /**
     * 在静态上下文中访问容器的魔法方法
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public static function __callStatic($name, $args)
    {
        $value = static::$instance->offsetGet($name);
        return $args ? call_user_func_array($value, $args) : $value;
    }
}
