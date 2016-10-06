<?php

function double(array $items)
{
    $result = [];
    foreach ($items as $item) {
        $result[] = $item * 2;
    }
    return $result;
}

function square(array $items)
{
    $result = [];
    foreach ($items as $item) {
        $result[] = $item * $item;
    }
    return $result;
}

class Demo03Test extends PHPUnit_Framework_TestCase
{
    private $items = [5, 12, 3, 4, 9];

    public function testDoubler()
    {
        $result = double($this->items);

        $this->assertEquals([10, 24, 6, 8, 18], $result);
    }

    public function testSquare()
    {
        $result = square($this->items);

        $this->assertEquals([25, 144, 9, 16, 81], $result);
    }
}