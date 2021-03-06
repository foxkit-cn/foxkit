<?php

namespace Foxkit\Routing\Request;

interface ParamFetcherInterface
{
    /**
     * Get a validated parameter.
     *
     * @param  string $index
     * @return mixed
     */
    public function get($index);
}
