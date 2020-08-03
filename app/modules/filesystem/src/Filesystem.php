<?php

namespace Foxkit\Filesystem;

use Foxkit\Filesystem\Adapter\AdapterInterface;
use Foxkit\Routing\Generator\UrlGenerator;

class Filesystem
{
    /**
     * @var AdapterInterface[]
     */
    protected $adapters = [];

    /**
     * 获取文件路径 URL
     *
     * @param string $file
     * @param mixed $referenceType
     * @return string|false
     */
    public function getUrl($file, $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        if (!$url = $this->getPathInfo($file, 'url')) {
            return false;
        }
        if ($referenceType === UrlGenerator::ABSOLUTE_PATH) {
            $url = strlen($path = parse_url($url, PHP_URL_PATH)) > 1 ? substr($url, strpos($url, $path)) : '/';
        } elseif ($referenceType === UrlGenerator::NETWORK_PATH) {
            $url = substr($url, strpos($url, '//'));
        }
        return $url;
    }

    /**
     * 获取规范化的文件路径或本地路径
     *
     * @param string $file
     * @param bool $local
     * @return string|false
     */
    public function getPath($file, $local = false)
    {
        return $this->getPathInfo($file, $local ? 'localpath' : 'pathname') ?: false;
    }

    /**
     * 获取文件路径信息
     *
     * @param string $file
     * @param string $option
     * @return string|array
     */
    public function getPathInfo($file, $option = null)
    {
        $info = Path::parse($file);
        if ($info['protocol'] != 'file') {
            $info['url'] = $info['pathname'];
        }
        if ($adapter = $this->getAdapter($info['protocol'])) {
            $info = $adapter->getPathInfo($info);
        }
        if ($option === null) {
            return $info;
        }
        return array_key_exists($option, $info) ? $info[$option] : '';
    }

    /**
     * 检查文件或目录是否存在
     *
     * @param string|array $files
     * @return bool
     */
    public function exists($files)
    {
        $files = (array)$files;
        foreach ($files as $file) {
            $file = $this->getPathInfo($file, 'pathname');
            if (!file_exists($file)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 复制一个文件
     *
     * @param string $source
     * @param string $target
     * @return bool
     */
    public function copy($source, $target)
    {
        $source = $this->getPathInfo($source, 'pathname');
        $target = $this->getPathInfo($target);
        if (!is_file($source) || !$this->makeDir($target['dirname'])) {
            return false;
        }
        return @copy($source, $target['pathname']);
    }

    /**
     * 删除一个文件
     *
     * @param string|array $files
     * @return bool
     */
    public function delete($files)
    {
        $files = (array)$files;
        foreach ($files as $file) {
            $file = $this->getPathInfo($file, 'pathname');
            if (is_dir($file)) {
                if (substr($file, -1) != '/') {
                    $file .= '/';
                }
                foreach ($this->listDir($file) as $name) {
                    if (!$this->delete($file . $name)) {
                        return false;
                    }
                }
                if (!@rmdir($file)) {
                    return false;
                }
            } elseif (!@unlink($file)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 列出指定路径内的文件和目录
     *
     * @param string $dir
     * @return array
     */
    public function listDir($dir)
    {
        $dir = $this->getPathInfo($dir, 'pathname');
        return array_diff(scandir($dir) ?: [], ['..', '.']);
    }

    /**
     * 建立一个目录
     *
     * @param string $dir
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function makeDir($dir, $mode = 0777, $recursive = true)
    {
        $dir = $this->getPathInfo($dir, 'pathname');
        return is_dir($dir) ? true : @mkdir($dir, $mode, $recursive);
    }

    /**
     * 复制一个目录
     *
     * @param string $source
     * @param string $target
     * @return bool
     */
    public function copyDir($source, $target)
    {
        $source = $this->getPathInfo($source, 'pathname');
        $target = $this->getPathInfo($target, 'pathname');
        if (!is_dir($source) || !$this->makeDir($target)) {
            return false;
        }
        if (substr($source, -1) != '/') {
            $source .= '/';
        }
        if (substr($target, -1) != '/') {
            $target .= '/';
        }
        foreach ($this->listDir($source) as $file) {
            if (is_dir($source . $file)) {
                if (!$this->copyDir($source . $file, $target . $file)) {
                    return false;
                }
            } elseif (!$this->copy($source . $file, $target . $file)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取一个 adapter
     *
     * @param string $protocol
     * @return AdapterInterface|null
     */
    public function getAdapter($protocol)
    {
        return isset($this->adapters[$protocol]) ? $this->adapters[$protocol] : null;
    }

    /**
     * 注册一个 adapter
     *
     * @param string $protocol
     * @param AdapterInterface $adapter
     */
    public function registerAdapter($protocol, AdapterInterface $adapter)
    {
        $this->adapters[$protocol] = $adapter;
        if ($wrapper = $adapter->getStreamWrapper()) {
            stream_wrapper_register($protocol, $wrapper);
        }
    }
}
