<?php

namespace Foxkit\Event;

use Foxkit\Util\Arr;

class Event implements EventInterface, \ArrayAccess
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var bool
     */
    protected $propagationStopped = false;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @param string $name
     * @param array $parameters
     */
    public function __construct($name, array $parameters = [])
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 设置事件名称
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 获取所有参数
     *
     * @return array|object|\ArrayAccess
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * 设置所有参数
     *
     * @param array
     * @return self
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param mixed $values
     * @param bool $replace
     * @return self
     */
    public function addParameters(array $values, $replace = false)
    {
        $this->parameters = Arr::merge($this->parameters, $values, $replace);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * 设置事件发送器
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    /**
     * 检查是否存在参数
     *
     * @param string $name
     * @return mixed
     */
    public function offsetExists($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * 获取一个参数
     *
     * @param string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * 设置一个参数
     *
     * @param string $name
     * @param callable $callback
     */
    public function offsetSet($name, $callback)
    {
        $this->parameters[$name] = $callback;
    }

    /**
     * 取消设置参数
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        unset($this->parameters[$name]);
    }
}
