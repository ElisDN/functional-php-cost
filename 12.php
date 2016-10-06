<?php

function createSimpleCost()
{
    return function (array $items) {
        $itemCost = function ($item) {
            return $item['price'] * $item['count'];
        };

        return array_sum(
            array_map($itemCost, $items));
    };
}

function createPriceBetweenCost(callable $next, $min, $max, $percent)
{
    return function (array $items) use ($next, $min, $max, $percent) {
        $discount = function ($item) use ($percent) { return ($percent / 100) * $item['price'] * $item['count']; };
        $filter = function ($item) use ($min, $max) { return $min <= $item['price'] && $item['price'] <= $max; };

        return $next($items) - array_sum(
            array_map($discount,
                array_filter($items, $filter)));
    };
}

function createMonthCost(callable $next, $day, $needle, $percent)
{
    return function (array $items) use ($next, $day, $needle, $percent) {
        return ($day == $needle) ? (1 - $percent / 100) * $next($items) : $next($items);
    };
}

function createBigCost(callable $next, $limit, $percent)
{
    return function (array $items) use ($next, $limit, $percent) {
        $cost = $next($items);
        return ($cost >= $limit) ? (1 - $percent / 100) * $cost : $cost;
    };
}

##################################

$simpleCost = createSimpleCost();
$priceBetweenCost = createPriceBetweenCost($simpleCost, 100, 150, 9);
$monthCost = createMonthCost($priceBetweenCost, date('d'), 15, 5);
$cost = createBigCost($monthCost, 1000, 7);

##################################

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

echo $cost($items) . PHP_EOL;