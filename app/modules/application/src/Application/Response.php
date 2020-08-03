<?php

namespace Foxkit\Application;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Response
{
    /**
     * @var UrlProvider
     */
    protected $url;

    /**
     * @param UrlProvider $url
     */
    public function __construct(UrlProvider $url)
    {
        $this->url = $url;
    }

    /**
     * 创建快捷方式
     *
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return HttpResponse
     * @see create()
     */
    public function __invoke($content = '', $status = 200, $headers = [])
    {
        return $this->create($content, $status, $headers);
    }

    /**
     * 返回一个响应
     *
     * @param mixed $content
     * @param int $status
     * @param array $headers
     * @return HttpResponse
     */
    public function create($content = '', $status = 200, $headers = [])
    {
        return new HttpResponse($content, $status, $headers);
    }

    /**
     * 返回一个重定向响应
     *
     * @param string $url
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect($url, $parameters = [], $status = 302, $headers = [])
    {
        return new RedirectResponse($this->url->get($url, $parameters), $status, $headers);
    }

    /**
     * 返回一个 JSON 响应
     *
     * @param string|array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    public function json($data = [], $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * 返回一个 streamed 响应
     *
     * @param callable $callback
     * @param int $status
     * @param array $headers
     * @return StreamedResponse
     */
    public function stream(callable $callback, $status = 200, $headers = [])
    {
        return new StreamedResponse($callback, $status, $headers);
    }

    /**
     * 返回一个二进制文件下载响应
     *
     * @param string $file
     * @param string $name
     * @param array $headers
     * @return BinaryFileResponse
     */
    public function download($file, $name = null, $headers = [])
    {
        $response = new BinaryFileResponse($file, 200, $headers, true, 'attachment');
        if (!is_null($name)) {
            $response->setContentDisposition('attachment', $name);
        }
        return $response;
    }
}
