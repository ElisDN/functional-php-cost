<?php

################################## Framework

function createSimpleCost(callable $itemCost)
{
    return function (array $items) use ($itemCost) {
        return array_sum(
            array_map($itemCost, $items));
    };
}

function createPriceBetweenCost(callable $next, $min, $max, $percent, callable $itemCost, callable $itemPrice)
{
    return function (array $items) use ($next, $min, $max, $percent, $itemCost, $itemPrice) {
        $discount = function ($item) use ($percent, $itemCost) { return ($percent / 100) * $itemCost($item); };
        $filter = function ($item) use ($min, $max, $itemPrice) {
            $price = $itemPrice($item);
            return $min <= $price && $price <= $max;
        };

        return $next($items) - array_sum(
            array_map($discount,
                array_filter($items, $filter)));
    };
}

function createCriteriaCost(callable $next, callable $criteria, $percent)
{
    return function (array $items) use ($next, $criteria, $percent) {
        $cost = $next($items);
        return $criteria($cost) ? (1 - $percent / 100) * $cost : $cost;
    };
}

function createMinCost(array $costs)
{
    return function (array $items) use ($costs) {
        return min(
            array_map(function ($cost) use ($items) { return $cost($items); }, $costs));
    };
}

################################## Tools

function memoize(callable $func)
{
    return function() use ($func) {
        static $memory = [];

        $args = func_get_args();
        $key = md5(serialize($args));

        if (!array_key_exists($key, $memory)) {
            $memory[$key] = call_user_func_array($func, $args);
        }

        return $memory[$key];
    };
}

################################## User methods

function createMonthCost(callable $next, $day, $needle, $percent) {
    return createCriteriaCost($next, function () use ($day, $needle) {
        return $day == $needle;
    }, $percent);
}

function createBirthdayCost(callable $next, $day, $needle, $percent) {
    return createCriteriaCost($next, function () use ($day, $needle) {
        return $day == $needle;
    }, $percent);
}

function createNewYearCost(callable $next, $month, $day, $percent) {
    return createCriteriaCost($next, function () use ($month, $day) {
        return $month == 12 && $day > 20;
    }, $percent);
}

function createBigCost(callable $next, $limit, $percent) {
    return createCriteriaCost($next, function ($cost) use ($limit) {
        return $cost >= $limit;
    }, $percent);
}

################################## Infrastructure

$itemCost = function ($item) { return $item['price'] * $item['count']; };
$itemPrice = function ($item) { return $item['price']; };

################################## Initialization

$simpleCost = createSimpleCost($itemCost);
$priceBetweenCost = createPriceBetweenCost($simpleCost, 100, 150, 9, $itemCost, $itemPrice);

$memoizedPriceBetweenCost = memoize($priceBetweenCost);

$monthCost = createMonthCost($memoizedPriceBetweenCost, date('d'), 15, 5);
$birthdayCost = createBirthdayCost($memoizedPriceBetweenCost, date('m-d'), '08-12', 6);
$newYearCost = createNewYearCost($memoizedPriceBetweenCost, date('m'), date('d'), 7);

$minCost = createMinCost([$monthCost, $birthdayCost, $newYearCost]);
$cost = createBigCost($minCost, 1000, 7);

################################## Calculation

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

echo $cost($items) . PHP_EOL;

/*
 *              big
 *               |
 *              min
 *          /    |     \
 *    month  birthday  newYear
 *          \    |     /
 *            between
 *               |
 *            simple
 */