<?php

namespace Foxkit;

use Foxkit\Application\Traits\EventTrait;
use Foxkit\Application\Traits\RouterTrait;
use Foxkit\Application\Traits\StaticTrait;
use Foxkit\Event\EventDispatcher;
use Foxkit\Module\ModuleManager;
use Symfony\Component\HttpFoundation\Request;

class Application extends Container
{
    use StaticTrait, EventTrait, RouterTrait;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);
        $this['events'] = function () {
            return new EventDispatcher();
        };
        $this['module'] = function () {
            return new ModuleManager($this);
        };
    }

    /**
     * 启动所有模块
     */
    public function boot()
    {
        if (!$this->booted) {
            $this->booted = true;
            $this->trigger('boot', [$this]);
        }
    }

    /**
     * 处理请求
     *
     * @param Request $request
     */
    public function run(Request $request = null)
    {
        if ($request === null) {
            $request = Request::createFromGlobals();
        }
        if (!$this->booted) {
            $this->boot();
        }
        $response = $this['kernel']->handle($request);
        $response->send();
        $this['kernel']->terminate($request, $response);
    }

    /**
     * 检查是否在控制台中运行
     *
     * @return bool
     */
    public function inConsole()
    {
        return PHP_SAPI == 'cli';
    }
}
