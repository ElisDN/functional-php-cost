<?php

function map($func, array $items)
{
    $result = [];
    foreach ($items as $item) {
        $result[] = $func($item);
    }
    return $result;
}

class Demo06Test extends PHPUnit_Framework_TestCase
{
    private $items = [5, 12, 3, 4, 17];

    public function testDouble()
    {
        $double = function ($item) {
            return $item * 2;
        };

        $result = map($double, $this->items);

        $this->assertEquals([10, 24, 6, 8, 34], $result);
    }

    public function testSquare()
    {
        $square = function ($item) {
            return $item * $item;
        };

        $result = map($square, $this->items);

        $this->assertEquals([25, 144, 9, 16, 17*17], $result);
    }
}

/*
function map_recursive($func, array $items)
{
    if ((count($items) > 1)) {
        return array_merge([$func(reset($items))], map_recursive($func, right($items)));
    } else {
        return [$func(reset($items))];
    }
}

function right($items) { return array_slice($items, 1); }
*/