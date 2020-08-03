<?php

namespace Foxkit\Installer\Package;

use Foxkit\Application as App;

class PackageFactory implements \ArrayAccess, \IteratorAggregate
{
    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @var array
     */
    protected $packages = [];

    /**
     * 获取快捷方式
     *
     * @param $name
     * @return mixed|null
     * @see get()
     */
    public function __invoke($name)
    {
        return $this->get($name);
    }

    /**
     * 获取一个 package
     *
     * @param string $name
     * @param bool $force
     * @return mixed|null
     */
    public function get($name, $force = false)
    {
        if ($force || empty($this->packages)) {
            $this->loadPackages();
        }
        return isset($this->packages[$name]) ? $this->packages[$name] : null;
    }

    /**
     * 获取所有 packages
     *
     * @param string $type
     * @param bool $force
     * @return array
     */
    public function all($type = null, $force = false)
    {
        if ($force || empty($this->packages)) {
            $this->loadPackages();
        }
        $filter = function ($package) use ($type) {
            return $package->get('type') == $type;
        };
        if ($type !== null) {
            $packages = array_filter($this->packages, $filter);
        } else {
            $packages = $this->packages;
        }
        return $packages;
    }

    /**
     * 从数据中加载一个 package
     *
     * @param string|array $data
     * @return Package
     */
    public function load($data)
    {
        if (is_string($data) && strpos($data, '{') !== 0) {
            $path = strtr(dirname($data), '\\', '/');
            $data = @file_get_contents($data);
        }
        if (is_string($data)) {
            $data = @json_decode($data, true);
        }
        if (is_array($data) && isset($data['name'])) {
            if (!isset($data['module'])) {
                $data['module'] = basename($data['name']);
            }
            if (isset($path)) {
                $data['path'] = $path;
                $data['url'] = App::url()->getStatic($path);
            }
            return new Package($data);
        }
    }

    /**
     * 添加一个 package 的路径
     *
     * @param string|array $paths
     * @return self
     */
    public function addPath($paths)
    {
        $this->paths = array_merge($this->paths, (array)$paths);
        return $this;
    }

    /**
     * 检查一个 package 是否存在
     *
     * @param string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($this->packages[$name]);
    }

    /**
     * 按名称获取一个 package
     *
     * @param string $name
     * @return bool
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * 设置一个 package
     *
     * @param string $name
     * @param string $package
     */
    public function offsetSet($name, $package)
    {
        $this->packages[$name] = $package;
    }

    /**
     * 取消设置 package
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        unset($this->packages[$name]);
    }

    /**
     * 执行 IteratorAggregate
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->packages);
    }

    /**
     * 从路径加载 package
     */
    protected function loadPackages()
    {
        foreach ($this->paths as $path) {
            $paths = glob($path, GLOB_NOSORT) ?: [];
            foreach ($paths as $p) {
                if (!$package = $this->load($p)) {
                    continue;
                }
                $this->packages[$package->getName()] = $package;
            }
        }
    }
}
