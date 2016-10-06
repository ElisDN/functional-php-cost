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
        $cost = $next($items);
        return $criteria($cost) ? (1 - $percent / 100) * $next($items) : $next($items);
    };
}

function createMinCost(array $costs)
{
    return function (array $items) use ($costs) {
        return min(
            array_map(function ($cost) use ($items) { return $cost($items); }, $costs));
    };
}

################################## Initialization

$simpleCost = createSimpleCost();
$priceBetweenCost = createPriceBetweenCost($simpleCost, 100, 150, 9);

$monthCriteria = function () { return date('d') == 15; };
$monthCost = createCriteriaCost($priceBetweenCost, $monthCriteria, 5);

$birthdayCriteria = function () { return date('m-d') == '08-12'; };
$birthdayCost = createCriteriaCost($priceBetweenCost, $birthdayCriteria, 6);

$newYearCriteria = function () { return date('m') == 12 && date('d') > 20; };
$newYearCost = createCriteriaCost($priceBetweenCost, $newYearCriteria, 7);

$minCost = createMinCost([$monthCost, $birthdayCost, $newYearCost]);

$bigCriteria = function ($cost) { return $cost >= 1000; };
$cost = createCriteriaCost($minCost, $bigCriteria, 7);

################################## Calculation

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

echo $cost($items) . PHP_EOL;