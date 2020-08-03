<?php

namespace Foxkit\Event;

interface EventDispatcherInterface
{
    /**
     * 添加一个事件监听
     *
     * @param string $event
     * @param callable $listener
     * @param int $priority
     */
    public function on($event, $listener, $priority = 0);

    /**
     * 移除一个或多个事件监听
     *
     * @param string $event
     * @param callable $listener
     */
    public function off($event, $listener = null);

    /**
     * 添加一个事件订阅
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function subscribe(EventSubscriberInterface $subscriber);

    /**
     * 移除一个事件订阅
     *
     * @param EventSubscriberInterface $subscriber
     */
    public function unsubscribe(EventSubscriberInterface $subscriber);

    /**
     * 触发一个事件
     *
     * @param string|EventInterface $event
     * @param array $arguments
     * @return EventInterface
     */
    public function trigger($event, array $arguments = []);

    /**
     * 检查事件是否有监听
     *
     * @param string $event
     * @return bool
     */
    public function hasListeners($event = null);

    /**
     * 获取一个事件的所有监听
     *
     * @param string $event
     * @return array
     */
    public function getListeners($event = null);

    /**
     * 获取指定事件的监听优先级
     *
     * @param string $event
     * @param callable $listener
     * @return int|null 事件监听优先级
     */
    public function getListenerPriority($event, $listener);

    /**
     * 获取默认的事件类
     *
     * @return string
     */
    public function getEventClass();
}
