<?php

namespace Foxkit\Filter;

/**
 * This filter converts the value to string.
 */
class StringFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        return (string) $value;
    }
}
