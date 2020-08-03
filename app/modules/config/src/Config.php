<?php

namespace Foxkit\Config;

use Foxkit\Util\Arr;

class Config implements \ArrayAccess, \Countable, \JsonSerializable
{
    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var bool
     */
    protected $dirty = false;

    /**
     * @param mixed $values
     */
    public function __construct($values = [])
    {
        $this->values = (array)$values;
    }

    /**
     * 检查给定 key 是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->values, $key);
    }

    /**
     * 通过 key 获得一个值
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->values, $key, $default);
    }

    /**
     * 设定一个值
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function set($key, $value)
    {
        Arr::set($this->values, $key, $value);
        $this->dirty = true;
        return $this;
    }

    /**
     * 删除一个或多个值
     *
     * @param array|string $keys
     * @return self
     */
    public function remove($keys)
    {
        Arr::remove($this->values, $keys);
        $this->dirty = true;
        return $this;
    }

    /**
     * 将数值推送到数组的末端
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function push($key, $value)
    {
        $values = $this->get($key);
        $values[] = $value;
        return $this->set($key, $values);
    }

    /**
     * 从数组中删除一个值
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function pull($key, $value)
    {
        $values = $this->get($key);
        Arr::pull($values, $value);
        return $this->set($key, $values);
    }

    /**
     * 合并另一个数组的值
     *
     * @param mixed $values
     * @param bool $replace
     * @return self
     */
    public function merge($values, $replace = false)
    {
        $this->values = Arr::merge($this->values, $values, $replace);
        $this->dirty = true;
        return $this;
    }

    /**
     * 提取配置值
     *
     * @param array $keys
     * @param bool $include
     * @return array
     */
    public function extract($keys, $include = true)
    {
        return Arr::extract($this->values, $keys, $include);
    }

    /**
     * 检查数值是否被修改
     *
     * @return bool
     */
    public function dirty()
    {
        return $this->dirty;
    }

    /**
     * 将数值转储为 php
     *
     * @return string
     */
    public function dump()
    {
        return '<?php return ' . var_export($this->values, true) . ';';
    }

    /**
     * 获取值长度
     *
     * @return int
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * 以普通数组的形式获取数值
     *
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * 实现 JsonSerializable 接口
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->values;
    }

    /**
     * 实现 ArrayAccess 接口
     *
     * @param $key
     * @return bool
     * @see has()
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * 实现 ArrayAccess 接口
     *
     * @param $key
     * @return mixed
     * @see get()
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * 实现 ArrayAccess 接口
     *
     * @param $key
     * @param $value
     * @see set()
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * 实现 ArrayAccess 接口
     *
     * @param $key
     * @see remove()
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }
}
