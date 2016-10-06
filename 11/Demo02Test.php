<?php

function sum(array $items)
{
    if (count($items) > 1) {
        return sum(left($items)) + end($items);
    } else {
        return reset($items);
    }
}

class Demo02Test extends PHPUnit_Framework_TestCase
{
    private $items = [5, 12, 3, 61, 17, 24, 11, 23];

    public function testSum()
    {
        $result = sum($this->items);

        $this->assertEquals(156, $result);
    }
}

function left($items) { return array_slice($items, 0, -1); }