<?php

function odd(array $items)
{
    $result = [];
    foreach ($items as $item) {
        if ($item % 2 == 1) {
            $result[] = $item;
        }
    }
    return $result;
}

function even(array $items)
{
    $result = [];
    foreach ($items as $item) {
        if ($item % 2 == 0) {
            $result[] = $item;
        }
    }
    return $result;
}

class Demo04Test extends PHPUnit_Framework_TestCase
{
    private $items = [5, 12, 3, 14, 17];

    public function testOdd()
    {
        $result = sum($this->items);

        $this->assertEquals([5, 3, 17], $result);
    }

    public function testEven()
    {
        $result = sum($this->items);

        $this->assertEquals([12, 14], $result);
    }
}