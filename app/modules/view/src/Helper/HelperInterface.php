<?php

namespace Foxkit\View\Helper;

use Foxkit\View\View;

interface HelperInterface
{
    /**
     * Registers the helper.
     *
     * @param View $view
     */
    public function register(View $view);

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName();
}
