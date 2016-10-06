<?php

function sum(array $items)
{
    $cost = 0;
    foreach ($items as $price) {
        $cost += $price;
    }
    return $cost;
}

class Demo01Test extends PHPUnit_Framework_TestCase
{
    private $items = [5, 12, 3, 61, 17, 24, 11, 23];

    public function testSum()
    {
        $result = sum($this->items);

        $this->assertEquals(156, $result);
    }
}