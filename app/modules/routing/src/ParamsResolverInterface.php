<?php

namespace Foxkit\Routing;

interface ParamsResolverInterface
{
    /**
     * 路由匹配后回调修改参数
     *
     * @param array $parameters
     * @return array
     */
    public function match(array $parameters = []);

    /**
     * 在生成 URL 时回调修改参数
     *
     * @param array $parameters
     * @return array
     */
    public function generate(array $parameters = []);
}
