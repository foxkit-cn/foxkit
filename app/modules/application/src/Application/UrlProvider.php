<?php

namespace Foxkit\Application;

use Foxkit\Filesystem\Filesystem;
use Foxkit\Filesystem\Locator;
use Foxkit\Routing\Generator\UrlGenerator;
use Foxkit\Routing\Router;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class UrlProvider
{
    /**
     * 生成一个相对于已执行脚本的路径，例如："/dir/file".
     */
    const BASE_PATH = 'base';

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Filesystem
     */
    protected $file;

    /**
     * @var Locator
     */
    protected $locator;

    /**
     * @param Router $router
     * @param Filesystem $file
     * @param Locator $locator
     */
    public function __construct(Router $router, Filesystem $file, Locator $locator)
    {
        $this->router = $router;
        $this->file = $file;
        $this->locator = $locator;
    }

    /**
     * 获取快捷方式
     *
     * @param string $path
     * @param array $parameters
     * @param int $referenceType
     * @return bool|false|string
     * @see get()
     */
    public function __invoke($path = '', $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        return $this->get($path, $parameters, $referenceType);
    }

    /**
     * 获取当前请求的基本路径
     *
     * @param mixed $referenceType
     * @return string
     */
    public function base($referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $request = $this->router->getRequest();
        $url = $request->getBasePath();
        if ($referenceType === UrlGenerator::ABSOLUTE_URL) {
            $url = $request->getSchemeAndHttpHost() . $url;
        } elseif ($referenceType === self::BASE_PATH) {
            $url = '';
        }
        return $url;
    }

    /**
     * 获取当前请求的 URL
     *
     * @param mixed $referenceType
     * @return string
     */
    public function current($referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $request = $this->router->getRequest();
        $url = $request->getBaseUrl();
        if ($referenceType === UrlGenerator::ABSOLUTE_URL) {
            $url = $request->getSchemeAndHttpHost() . $url;
        }
        if ($qs = $request->getQueryString()) {
            $qs = '?' . $qs;
        }
        return $url . $request->getPathInfo() . $qs;
    }

    /**
     * 获取前一个请求的 URL
     *
     * @return string
     */
    public function previous()
    {
        return $this->router->getRequest()->headers->get('referer');
    }

    /**
     * 获取将 URI 附加到基本 URI 上的 URL
     *
     * @param string $path
     * @param mixed $parameters
     * @param mixed $referenceType
     * @return string
     */
    public function get($path = '', $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        if (0 === strpos($path, '@')) {
            return $this->getRoute($path, $parameters, $referenceType);
        }
        $path = $this->parseQuery($path, $parameters);
        if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
            return $path;
        }
        return $this->base($referenceType) . '/' . ltrim($path, '/');
    }

    /**
     * 获取指定途径的 URL
     *
     * @param string $name
     * @param mixed $parameters
     * @param mixed $referenceType
     * @return string|false
     */
    public function getRoute($name, $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        try {
            $url = $this->router->generate($name, $parameters, $referenceType === self::BASE_PATH ? UrlGenerator::ABSOLUTE_PATH : $referenceType);
            if ($referenceType === self::BASE_PATH) {
                $url = substr($url, strlen($this->router->getRequest()->getBaseUrl()));
            }
            return $url;

        } catch (RouteNotFoundException $e) {
        } catch (MissingMandatoryParametersException $e) {
        } catch (InvalidParameterException $e) {
        }
        return false;
    }

    /**
     * 获取路径资源的 URL
     *
     * @param string $path
     * @param mixed $parameters
     * @param mixed $referenceType
     * @return string
     */
    public function getStatic($path, $parameters = [], $referenceType = UrlGenerator::ABSOLUTE_PATH)
    {
        $url = $this->file->getUrl($this->locator->get($path) ?: $path, $referenceType === self::BASE_PATH ? UrlGenerator::ABSOLUTE_PATH : $referenceType);
        if ($referenceType === self::BASE_PATH) {
            $url = substr($url, strlen($this->router->getRequest()->getBasePath()));
        }
        return $this->parseQuery($url, $parameters);
    }

    /**
     * 将查询参数解析为一个 URL
     *
     * @param string $url
     * @param array $parameters
     * @return string
     */
    protected function parseQuery($url, $parameters = [])
    {
        if ($query = substr(strstr($url, '?'), 1)) {
            parse_str($query, $params);
            $url = strstr($url, '?', true);
            $parameters = array_replace($parameters, $params);
        }
        if ($query = http_build_query($parameters, '', '&')) {
            $url .= '?' . $query;
        }
        return $url;
    }
}
