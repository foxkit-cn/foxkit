<?php

namespace Foxkit\Filesystem\Adapter;

interface AdapterInterface
{
    /**
     * 获取 stream wrapper 类名
     *
     * @return string
     */
    public function getStreamWrapper();

    /**
     * 获取文件路径信息
     *
     * @param array $info
     * @return array
     */
    public function getPathInfo(array $info);
}
