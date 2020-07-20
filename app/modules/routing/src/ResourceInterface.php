<?php

namespace Foxkit\Routing;

interface ResourceInterface extends \Serializable
{
    /**
     * Gets the resources modified time.
     *
     * @return int
     */
    public function getModified();
}
