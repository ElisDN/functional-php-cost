<?php

function sum(array $items)
{
    $total = 0;
    foreach ($items as $current) {
        $total = $total + $current;
    }
    return $total;
}

function product(array $items)
{
    $total = 0;
    foreach ($items as $current) {
        $total = $total * $current;
    }
    return $total;
}

class Demo05Test extends PHPUnit_Framework_TestCase
{
    private $items = [5, 12, 3, 61, 17, 24, 11, 23];

    public function testSum()
    {
        $result = sum($this->items);

        $this->assertEquals(156, $result);
    }

    public function testProduct()
    {
        $result = product($this->items);

        $this->assertEquals(1133399520, $result);
    }
}

function left($items) { return array_slice($items, 0, -1); }