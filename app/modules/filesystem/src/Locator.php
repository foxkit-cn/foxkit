<?php

namespace Foxkit\Filesystem;

class Locator
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $path = strtr($path, '\\', '/');
        if (substr($path, -1) != '/') {
            $path .= '/';
        }
        $this->path = $path;
    }

    /**
     * 添加文件路径到 locator
     *
     * @param string $prefix
     * @param string|array $paths
     * @return self
     */
    public function add($prefix, $paths)
    {
        $paths = array_map(function ($path) use ($prefix) {
            $path = strtr($path, '\\', '/');
            if (substr($path, -1) != '/') {
                $path .= '/';
            }
            return [$prefix, $path];
        }, (array)$paths);
        $this->paths = array_merge($paths, $this->paths);
        return $this;
    }

    /**
     * 从 locator 中获取一个文件路径
     *
     * @param string $file
     * @return string|false
     */
    public function get($file)
    {
        $file = ltrim(strtr($file, '\\', '/'), '/');
        $paths = array_merge($this->paths, [['', $this->path]]);
        foreach ($paths as $parts) {
            list($prefix, $path) = $parts;
            if ($prefix !== '' && strpos($file, $prefix) !== 0) {
                continue;
            }
            if (($part = substr($file, strlen($prefix))) !== false) {
                $path .= ltrim($part, '/');
            }
            if (file_exists($path)) {
                return $path;
            }
        }
        return false;
    }
}
