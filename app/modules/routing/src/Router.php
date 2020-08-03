<?php

namespace Foxkit\Routing;

use Foxkit\Routing\Generator\UrlGenerator;
use Foxkit\Routing\Generator\UrlGeneratorDumper;
use Foxkit\Routing\Generator\UrlGeneratorInterface;
use Foxkit\Routing\Loader\LoaderInterface;
use Foxkit\Routing\RequestContext as Context;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherDumper;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class Router implements RouterInterface, UrlGeneratorInterface
{
    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var RequestStack
     */
    protected $stack;

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @var UrlMatcher
     */
    protected $matcher;

    /**
     * @var UrlGenerator
     */
    protected $generator;

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $cache;

    /**
     * @var ParamsResolverInterface[]
     */
    protected $resolver = [];

    /**
     * @param ResourceInterface $resource
     * @param LoaderInterface $loader
     * @param RequestStack $stack
     * @param array $options
     */
    public function __construct(ResourceInterface $resource, LoaderInterface $loader, RequestStack $stack, array $options = [])
    {
        $this->resource = $resource;
        $this->loader = $loader;
        $this->stack = $stack;
        $this->context = new Context();
        $this->options = array_replace([
            'cache' => null,
            'matcher' => 'Symfony\Component\Routing\Matcher\UrlMatcher',
            'generator' => 'Foxkit\Routing\Generator\UrlGenerator'
        ], $options);
    }

    /**
     * 获取当前的请求
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->stack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    /**
     * 获取路由的选项
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 设置路由的选项
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * 设置路由的一个选项
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * 获取一个路由
     *
     * @param string $name
     * @return \Symfony\Component\Routing\Route|null
     */
    public function getRoute($name)
    {
        return $this->getRouteCollection()->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        if (!$this->routes) {
            $this->routes = $this->loader->load($this->resource);
        }
        return $this->routes;
    }

    /**
     * 获取 URL 匹配实例
     *
     * @return UrlMatcher
     */
    public function getMatcher()
    {
        if (!$this->matcher) {
            if ($cache = $this->getCache('%s/%s.matcher.cache')) {
                $class = sprintf('UrlMatcher%s', $cache['key']);
                if (!$cache['fresh']) {
                    $options = ['class' => $class, 'base_class' => $this->options['matcher']];
                    $this->writeCache($cache['file'], (new PhpMatcherDumper($this->getRouteCollection()))->dump($options));
                }
                require_once $cache['file'];
                $this->matcher = new $class($this->context);
            } else {
                $class = $this->options['matcher'];
                $this->matcher = new $class($this->getRouteCollection(), $this->context);
            }
        }
        return $this->matcher;
    }

    /**
     * 获取与该路由相关联的 UrlGenerator 实例
     *
     * @return UrlGenerator
     */
    public function getGenerator()
    {
        if (!$this->generator) {
            if ($cache = $this->getCache('%s/%s.generator.cache')) {
                $class = sprintf('UrlGenerator%s', $cache['key']);
                if (!$cache['fresh']) {
                    $options = ['class' => $class, 'base_class' => $this->options['generator']];
                    $this->writeCache($cache['file'], (new UrlGeneratorDumper($this->getRouteCollection()))->dump($options));
                }
                require_once $cache['file'];
                $this->generator = new $class($this->context);
            } else {
                $class = $this->options['generator'];
                $this->generator = new $class($this->getRouteCollection(), $this->context);
            }
        }
        return $this->generator;
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
    public function redirect($url = '', $parameters = [], $status = 302, $headers = [])
    {
        try {
            $url = $this->generate($url, $parameters);
        } catch (RouteNotFoundException $e) {
            if (filter_var($url, FILTER_VALIDATE_URL) === false && strpos($url, '/') !== 0) {
                $url = "{$this->getRequest()->getBaseUrl()}/$url";
            }
        }
        return new RedirectResponse($url, $status, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        $this->context->fromRequest($this->getRequest());
        $params = $this->getMatcher()->match($pathinfo);
        if ($resolver = $this->getResolver($params)) {
            $params = $resolver->match($params);
        }
        if (false !== $pos = strpos($params['_route'], '?')) {
            $params['_route'] = substr($params['_route'], 0, $pos);
        }
        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        $generator = $this->getGenerator();
        if ($fragment = strstr($name, '#')) {
            $name = strstr($name, '#', true);
        }
        if ($query = substr(strstr($name, '?'), 1)) {
            parse_str($query, $params);
            $name = strstr($name, '?', true);
            $parameters = array_replace($parameters, $params);
        }
        if ($referenceType !== self::LINK_URL
            && ($props = $generator->getRouteProperties($generator->generate($name, $parameters, 'link')) or $props = $generator->getRouteProperties($name))
            && $resolver = $this->getResolver($props[1])
        ) {
            $parameters = $resolver->generate($parameters);
        }
        return $generator->generate($name, $parameters, $referenceType) . $fragment;
    }

    /**
     * 获取缓存信息
     *
     * @param string $file
     * @return array|null
     */
    protected function getCache($file)
    {
        if (!$this->options['cache']) {
            return null;
        }
        if (!$this->cache) {
            $this->cache = ['key' => sha1(serialize($this->resource) . serialize($this->options)), 'modified' => $this->resource->getModified()];
        }
        $file = sprintf($file, $this->options['cache'], $this->cache['key']);
        $fresh = file_exists($file) && (!$this->cache['modified'] || filemtime($file) >= $this->cache['modified']);
        return array_merge(compact('fresh', 'file'), $this->cache);
    }

    /**
     * 写入缓存文件
     *
     * @param string $file
     * @param string $content
     * @throws \RuntimeException
     */
    protected function writeCache($file, $content)
    {
        if (!file_put_contents($file, $content)) {
            throw new \RuntimeException("Failed to write cache file ($file).");
        }
    }

    /**
     * 从参数中获取解析器实例
     *
     * @param array $parameters
     * @return ParamsResolverInterface|null
     */
    protected function getResolver(array $parameters = [])
    {
        $resolver = isset($parameters['_resolver']) ? $parameters['_resolver'] : false;
        if (!isset($this->resolver[$resolver])) {
            if (!is_subclass_of($resolver, 'Foxkit\Routing\ParamsResolverInterface')) {
                return null;
            }
            $this->resolver[$resolver] = new $resolver;
        }
        return $this->resolver[$resolver];
    }
}
