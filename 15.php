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

function createCriteriaCost(callable $next, callable $criteria, $percent)
{
    return function (array $items) use ($next, $criteria, $percent) {
        return $criteria() ? (1 - $percent / 100) * $next($items) : $next($items);
    };
}

function createBigCost(callable $next, $limit, $percent)
{
    return function (array $items) use ($next, $limit, $percent) {
        $cost = $next($items);
        return ($cost >= $limit) ? (1 - $percent / 100) * $cost : $cost;
    };
}

function createMinCost(array $costs)
{
    return function (array $items) use ($costs) {
        return min(
            array_map(function ($cost) use ($items) { return $cost($items); }, $costs));
    };
}

##################################

$simpleCost = createSimpleCost();
$priceBetweenCost = createPriceBetweenCost($simpleCost, 100, 150, 9);

$monthCost = createCriteriaCost($priceBetweenCost, function () { return date('d') == 15; }, 5);
$birthdayCost = createCriteriaCost($priceBetweenCost, function () { return date('m-d') == '08-12'; }, 6);

$minCost = createMinCost([$monthCost, $birthdayCost]);
$cost = createBigCost($minCost, 1000, 7);

##################################

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

echo $cost($items) . PHP_EOL;

/*
 *              big
 *               |
 *              min
 *            /     \
 *        month   birthday
 *           \      /
 *           between
 *              |
 *            simple
 */