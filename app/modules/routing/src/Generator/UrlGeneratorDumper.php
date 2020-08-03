<?php

namespace Foxkit\Routing\Generator;

use Symfony\Component\Routing\Generator\Dumper\GeneratorDumper;

/**
 * UrlGeneratorDumper 创建了一个能够为给定的路由集生成 URL 的 PHP 类
 */
class UrlGeneratorDumper extends GeneratorDumper
{
    /**
     * 转储一组路由到一个 PHP 类。
     *
     * @param array $options
     * @return string
     */
    public function dump(array $options = [])
    {
        $options = array_merge([
            'class' => 'ProjectUrlGenerator',
            'base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
        ], $options);
        return <<<EOF
<?php

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Psr\Log\LoggerInterface;

/**
 * {$options['class']}
 *
 * 这个类是由 Symfony Routing 组件自动生成的
 */
class {$options['class']} extends {$options['base_class']}
{
    private static \$declaredRoutes = {$this->generateDeclaredRoutes()};

    public function __construct(RequestContext \$context, LoggerInterface \$logger = null)
    {
        \$this->context = \$context;
        \$this->logger = \$logger;
    }

{$this->generateGenerateMethod()}
}

EOF;
    }

    /**
     * 生成代表定义的路由数组和路由属性的 PHP 代码（例如：requirements）
     *
     * @return string
     */
    private function generateDeclaredRoutes()
    {
        $routes = "[\n";
        foreach ($this->getRoutes()->all() as $name => $route) {
            $compiledRoute = $route->compile();

            $properties = [];
            $properties[] = $compiledRoute->getVariables();
            $properties[] = $route->getDefaults();
            $properties[] = $route->getRequirements();
            $properties[] = $compiledRoute->getTokens();
            $properties[] = $compiledRoute->getHostTokens();
            $properties[] = $route->getSchemes();

            $routes .= sprintf("        '%s' => %s,\n", $name, str_replace("\n", '', var_export($properties, true)));
        }
        $routes .= '    ]';

        return $routes;
    }

    /**
     * 生成实现 UrlGeneratorInterface 的 `generate` 方法的 PHP 代码
     *
     * @return string
     */
    private function generateGenerateMethod()
    {
        return <<<EOF
    public function generate(\$name, \$parameters = [], \$referenceType = self::ABSOLUTE_PATH)
    {
        if (!isset(self::\$declaredRoutes[\$name])) {
            throw new RouteNotFoundException(sprintf('Unable to generate a URL for the named route "%s" as such route does not exist.', \$name));
        }

        list(\$variables, \$defaults, \$requirements, \$tokens, \$hostTokens, \$requiredSchemes) = self::\$declaredRoutes[\$name];

        return \$this->doGenerate(\$variables, \$defaults, \$requirements, \$tokens, \$parameters, \$name, \$referenceType, \$hostTokens, \$requiredSchemes);
    }

    public function getRouteProperties(\$name)
    {
        if (isset(self::\$declaredRoutes[\$name])) {
            return self::\$declaredRoutes[\$name];
        }
    }
EOF;
    }
}
