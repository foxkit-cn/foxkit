<?php

namespace Foxkit\Util;

class Arr
{
    const ARRAY_FILTER_USE_BOTH = 1;
    const ARRAY_FILTER_USE_KEY = 2;

    /**
     * 检查给定 key 是否存在
     *
     * @param array $array
     * @param string $key
     * @return bool
     */
    public static function has(array $array, $key)
    {
        if (!$array || $key === null) {
            return false;
        }
        if (array_key_exists($key, $array)) {
            return true;
        }
        $parts = explode('.', $key);
        foreach ($parts as $part) {
            if (!is_array($array) || !array_key_exists($part, $array)) {
                return false;
            }
            $array = $array[$part];
        }
        return true;
    }

    /**
     * 通过 key 获得一个值
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if ($key === null) {
            return $array;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }
        $parts = explode('.', $key);
        foreach ($parts as $part) {
            if (!is_array($array) || !array_key_exists($part, $array)) {
                return $default;
            }
            $array = $array[$part];
        }
        return $array;
    }

    /**
     * 设定一个值
     *
     * @param array $array
     * @param string $key
     * @param mixed $value
     * @return array
     */
    public static function set(array &$array, $key, $value)
    {
        if ($key === null) {
            return $array = $value;
        }
        $parts = explode('.', $key);
        while (count($parts) > 1) {
            $part = array_shift($parts);
            if (!isset($array[$part]) || !is_array($array[$part])) {
                $array[$part] = [];
            }
            $array =& $array[$part];
        }
        $array[array_shift($parts)] = $value;
        return $array;
    }

    /**
     * 从数组中，根据 key 删除一个值
     *
     * @param array $array
     * @param array|string $keys
     */
    public static function remove(array &$array, $keys)
    {
        $original =& $array;
        foreach ((array)$keys as $key) {
            $parts = explode('.', $key);
            while (count($parts) > 1) {
                $part = array_shift($parts);
                if (isset($array[$part]) && is_array($array[$part])) {
                    $array =& $array[$part];
                }
            }
            unset($array[array_shift($parts)]);
            $array =& $original;
        }
    }

    /**
     * 从数组中删除一个值
     *
     * @param array $array
     * @param mixed $value
     * @param bool $strict
     * @return array
     */
    public static function pull(array &$array, $value, $strict = false)
    {
        if ($keys = array_keys($array, $value, $strict)) {
            foreach ($keys as $key) {
                unset($array[$key]);
            }
            $new = array_values($array);
            $array =& $new;
        }
        return $array;
    }

    /**
     * 递归合并两个数组
     *
     * @param array $array1
     * @param array $array2
     * @param bool $replace
     * @return array
     */
    public static function merge(array $array1, array $array2, $replace = false)
    {
        if ($replace) {
            return array_replace_recursive($array1, $array2);
        }
        foreach ($array2 as $key => $value) {
            if (isset($array1[$key])) {
                if (is_int($key)) {
                    $array1[] = $value;
                } else if (is_array($value) && is_array($array1[$key])) {
                    $array1[$key] = static::merge($array1[$key], $value);
                } else {
                    $array1[$key] = $value;
                }
            } else {
                $array1[$key] = $value;
            }
        }
        return $array1;
    }

    /**
     * 使用回调函数过滤一个数组
     *
     * @param array $array
     * @param callable $callback
     * @param int $flag
     * @return array
     */
    public static function filter(array $array, callable $callback, $flag = 1)
    {
        if (version_compare(PHP_VERSION, '7.2.0') >= 0) {
            return array_filter($array, $callback, $flag);
        }
        $filtered = [];
        foreach ($array as $key => $value) {
            $args = [$value];
            if ($flag === static::ARRAY_FILTER_USE_BOTH) {
                $args = [$value, $key];
            }
            if ($flag === static::ARRAY_FILTER_USE_KEY) {
                $args = [$key];
            }
            if (call_user_func_array($callback, $args)) {
                $filtered[$key] = $value;
            }
        }
        return $filtered;
    }

    /**
     * 按 key 提取值
     *
     * @param array $data
     * @param array $keys
     * @param bool $include
     * @return array
     */
    public static function extract(array $data, array $keys = null, $include = true)
    {
        if (!$keys) {
            return $data;
        }
        $data = self::flatten($data);
        $result = [];
        foreach ($data as $keypath => $value) {
            $add = !$include;
            foreach ($keys as $key) {
                if (0 === strpos($keypath, $key)) {
                    $add = $include;
                    break;
                }
            }
            if ($add) {
                $result[$keypath] = $value;
            }
        }
        return self::expand($result);
    }

    /**
     * 扁平化一个数组
     *
     * @param array $array
     * @param string $path
     * @return array
     */
    public static function flatten(array $array, $path = '')
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, self::flatten($value, $path . $key . '.'));
            } else {
                $results[$path . $key] = $value;
            }
        }
        return $results;
    }

    /**
     * 展开一个数组
     *
     * @param array $array
     * @return array
     */
    public static function expand(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $values =& $result;
            $keys = explode('.', $key);
            while (count($keys) > 1) {
                $key = array_shift($keys);
                if (!isset($values[$key]) || !is_array($values[$key])) {
                    $values[$key] = [];
                }
                $values =& $values[$key];
            }
            $values[array_shift($keys)] = $value;
        }
        return $result;
    }
}
