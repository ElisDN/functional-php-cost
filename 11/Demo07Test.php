<?php

function filter($func, array $items)
{
    $result = [];
    foreach ($items as $item) {
        if ($func($item)) {
            $result[] = $item;
        }
    }
    return $result;
}

class Demo07Test extends PHPUnit_Framework_TestCase
{
    private $items = [5, 12, 3, 61, 17, 24];

    public function testOdd()
    {
        $odd = function ($item) {
            return $item % 2 == 1;
        };

        $result = filter($odd, $this->items);

        $this->assertEquals([5, 3, 61, 17], $result);
    }

    public function testEven()
    {
        $even = function ($item) {
            return $item % 2 == 0;
        };

        $result = filter($even, $this->items);

        $this->assertEquals([12, 24], $result);
    }
}

/*
function filter_recursive($func, array $items)
{
    if ((count($items) > 1)) {
        if ($func(reset($items))) {
            return array_merge([reset($items)], filter_recursive($func, right($items)));
        } else {
            return filter_recursive($func, right($items));
        }
    } else {
        if ($func(reset($items))) {
            return [reset($items)];
        } else {
            return [];
        }
    }
}

function left($items) { return array_slice($items, 0, -1); }
function right($items) { return array_slice($items, 1); }
*/