<?php

namespace Foxkit\Application\Traits;

use Foxkit\Event\EventSubscriberInterface;

trait EventTrait
{
    /**
     * @param $event
     * @param $callback
     * @param int $priority
     * @see EventDispatcher::on()
     */
    public static function on($event, $callback, $priority = 0)
    {
        if (static::$instance->booted) {
            static::events()->on($event, $callback, $priority);
            return;
        }
        static::$instance->extend('events', function ($dispatcher) use ($event, $callback, $priority) {
            $dispatcher->on($event, $callback, $priority);
            return $dispatcher;
        });

    }

    /**
     * @param EventSubscriberInterface $subscriber
     * @see EventDispatcher::subscribe()
     */
    public static function subscribe(EventSubscriberInterface $subscriber)
    {
        $subscribers = func_num_args() > 1 ? func_get_args() : [$subscriber];
        if (static::$instance->booted) {
            foreach ($subscribers as $sub) {
                static::events()->subscribe($sub);
            }
            return;
        }
        try {
            static::$instance->extend('events', function ($dispatcher) use ($subscribers) {
                foreach ($subscribers as $sub) {
                    $dispatcher->subscribe($sub);
                }
                return $dispatcher;
            });
        } catch (\Exception $e) {
            $test = $e;
        }
    }

    /**
     * @param $event
     * @param array $arguments
     * @return
     * @see EventDispatcher::trigger()
     */
    public static function trigger($event, array $arguments = [])
    {
        return static::events()->trigger($event, $arguments);
    }
}
