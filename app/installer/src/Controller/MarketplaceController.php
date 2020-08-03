<?php

namespace Foxkit\Installer\Controller;

use Foxkit\Application as App;

/**
 * @Access("system: manage packages", admin=true)
 */
class MarketplaceController
{

    /**
     * @Request({"page":"int"})
     */
    public function themesAction($page = null)
    {
        return [
            '$view' => [
                'title' => __('Marketplace'),
                'name' => 'installer:views/marketplace.php'
            ],
            '$data' => [
                'title' => 'Themes',
                'type' => 'foxkit-theme',
                'api' => App::get('system.api'),
                'installed' => array_values(App::package()->all('foxkit-theme')),
                'page' => $page
            ]
        ];
    }

    /**
     * @Request({"page":"int"})
     * @param null $page
     * @return array[]
     */
    public function extensionsAction($page = null)
    {
        return [
            '$view' => [
                'title' => __('Marketplace'),
                'name' => 'installer:views/marketplace.php'
            ],
            '$data' => [
                'title' => 'Extensions',
                'type' => 'foxkit-extension',
                'api' => App::get('system.api'),
                'installed' => array_values(App::package()->all('foxkit-extension')),
                'page' => $page
            ]
        ];
    }
}
