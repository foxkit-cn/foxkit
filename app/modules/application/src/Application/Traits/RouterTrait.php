<?php

namespace Foxkit\Application\Traits;

use Foxkit\Kernel\Event\ExceptionListenerWrapper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait RouterTrait
{
    /**
     * @param $code
     * @param null $message
     * @param array $headers
     * @see HttpKernel::abort()
     */
    public static function abort($code, $message = null, array $headers = [])
    {
        static::kernel()->abort($code, $message, $headers);
    }

    /**
     * 注册一个错误处理程序
     *
     * @param mixed $callback
     * @param integer $priority
     */
    public static function error($callback, $priority = -8)
    {
        static::events()->on('exception', new ExceptionListenerWrapper($callback), $priority);
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return
     * @see Router::redirect()
     */
    public static function redirect($url = '', $parameters = [], $status = 302, $headers = [])
    {
        return static::router()->redirect($url, $parameters, $status, $headers);
    }

    /**
     * 处理内部转发操作的子请求
     *
     * @param string $name
     * @param array $parameters
     * @return Response
     * @throws \RuntimeException
     */
    public static function forward($name, $parameters = [])
    {
        if (!$request = static::request()) {
            throw new \RuntimeException('No Request set.');
        }

        return static::kernel()->handle(
            Request::create(
                static::router()->generate($name, $parameters), 'GET', [],
                $request->cookies->all(), [],
                $request->server->all()
            ));
    }
}
