<?php

namespace Foxkit\Event;

interface EventSubscriberInterface
{
    /**
     * 返回 subscriber 想要监听的事件名称的数组
     *
     * @return array
     */
    public function subscribe();
}
