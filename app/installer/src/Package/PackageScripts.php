<?php

namespace Foxkit\Installer\Package;

use Foxkit\Application as App;

class PackageScripts
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $current;

    /**
     * @param string $file
     * @param string $current
     */
    public function __construct($file, $current = null)
    {
        $this->file = $file;
        $this->current = $current;
    }

    /**
     * 运行脚本的 install 钩子
     */
    public function install()
    {
        $this->run($this->get('install'));
    }

    /**
     *  运行脚本的 uninstall 钩子
     */
    public function uninstall()
    {
        $this->run($this->get('uninstall'));
    }

    /**
     *  运行脚本的 enable 钩子
     */
    public function enable()
    {
        $this->run($this->get('enable'));
    }

    /**
     *  运行脚本的 disable 钩子
     */
    public function disable()
    {
        $this->run($this->get('disable'));
    }

    /**
     *  运行脚本的 update 钩子
     */
    public function update()
    {
        $this->run($this->getUpdates());
    }

    /**
     * 检查脚本更新。
     */
    public function hasUpdates()
    {
        return (bool)$this->getUpdates();
    }

    /**
     * @param string $name
     * @return array
     */
    protected function get($name)
    {
        $scripts = $this->load();
        return isset($scripts[$name]) ? $scripts[$name] : [];
    }

    /**
     * @return array
     */
    protected function load()
    {
        if (!$this->file || !file_exists($this->file)) {
            return [];
        }
        return require $this->file;
    }

    /**
     * @param array|callable $scripts
     */
    protected function run($scripts)
    {
        array_map(function ($script) {
            if (is_callable($script)) {
                call_user_func($script, App::getInstance());
            }
        }, (array)$scripts);
    }

    /**
     * @return callable[]
     */
    protected function getUpdates()
    {
        $updates = $this->get('updates');
        $versions = array_filter(array_keys($updates), function ($version) {
            return version_compare($version, $this->current, '>');
        });
        $updates = array_intersect_key($updates, array_flip($versions));
        uksort($updates, 'version_compare');
        return $updates;
    }
}
