<?php

namespace Foxkit\Event;

interface EventInterface
{
    /**
     * 获取事件名称
     *
     * @return string
     */
    public function getName();

    /**
     * 获取事件调度器
     *
     * @return EventDispatcherInterface
     */
    public function getDispatcher();

    /**
     * 是否停止传播
     *
     * @return bool
     */
    public function isPropagationStopped();

    /**
     * 停止进一步的事件传播
     *
     * @return void
     */
    public function stopPropagation();
}
