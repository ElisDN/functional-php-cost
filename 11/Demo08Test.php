<?php

function reduce($func, array $items, $initial = null)
{
    $total = $initial;
    foreach ($items as $current) {
        $total = $func($total, $current);
    }
    return $total;
}

class Demo08Test extends PHPUnit_Framework_TestCase
{
    private $items = [5, 12, 3, 61, 17, 24, 11, 23];

    public function testSum()
    {
        $sum = function ($total, $current) {
            return $total + $current;
        };

        $result = reduce($sum, $this->items);

        $this->assertEquals(156, $result);
    }

    public function testMultiply()
    {
        $multiply = function ($total, $current) {
            return $total * $current;
        };

        $result = reduce($multiply, $this->items, 1);

        $this->assertEquals(1133399520, $result);
    }

    public function testMax()
    {
        $max = function ($total, $current) {
            return $current > $total ? $current : $total;
        };

        $result = reduce($max, $this->items, reset($this->items));

        $this->assertEquals(61, $result);
    }
}

/*
function reduce_recursive($func, array $items)
{
    if (count($items) > 1) {
        return $func(reduce_recursive($func, left($items)), end($items));
    } else {
        return reset($items);
    }
}

function left($items) { return array_slice($items, 0, -1); }
*/