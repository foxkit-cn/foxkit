<?php

namespace Foxkit\Dashboard;

use Foxkit\Application as App;
use Foxkit\Module\Module;

class DashboardModule extends Module
{
    /**
     * Gets a widget.
     *
     * @param  string $id
     * @return array
     */
    public function getWidget($id)
    {
        $widgets = $this->getWidgets();

        return isset($widgets[$id]) ? $widgets[$id] : null;
    }

    /**
     * Gets all user widgets.
     *
     * @return array
     */
    public function getWidgets()
    {
        return App::config()->get('system/dashboard', $this->config('defaults'))->toArray();
    }

    /**
     * Save widgets on user.
     *
     * @param array $widgets
     */
    public function saveWidgets(array $widgets)
    {
        App::config()->set('system/dashboard', $widgets);
    }
}
