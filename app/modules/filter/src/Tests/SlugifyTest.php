<?php

namespace Foxkit\Filter\Tests;

use Foxkit\Filter\SlugifyFilter;

class SlugifyTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new SlugifyFilter;

        $values = [
            'FOXKIT'                  => 'foxkit',
            ":#*\"@+=;!><&.%()/'\\|[]" => "",
            "  a b ! c   "             => "a-b-c",
        ];

        foreach ($values as $in => $out) {
            $this->assertEquals($out, $filter->filter($in));
        }

    }
}
