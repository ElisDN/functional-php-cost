<?php

// Partial applying instead of factory

################################## Framework

function simpleCost(callable $itemCost, array $items) {
    return array_sum(
        array_map($itemCost, $items));
};

function priceBetweenCost(callable $next, callable $itemCost, callable $itemPrice, $min, $max, $percent, $items)
{
    $discount = function ($item) use ($percent, $itemCost) { return ($percent / 100) * $itemCost($item); };
    $filter = function ($item) use ($min, $max, $itemPrice) {
        $price = $itemPrice($item);
        return $min <= $price && $price <= $max;
    };

    return $next($items) - array_sum(
        array_map($discount,
            array_filter($items, $filter)));
};

function criteriaCost(callable $next, callable $criteria, $percent, $items) {
    $cost = $next($items);
    return $criteria($cost) ? (1 - $percent / 100) * $cost : $cost;
};

function minCost(array $costs, $items) {
    return min(
        array_map(function ($cost) use ($items) { return $cost($items); }, $costs));
};

################################## Tools

function memoize(callable $func) {
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

function partial(callable $function) {
    $arguments = array_slice(func_get_args(), 1);
    return function() use ($function, $arguments) {
        return call_user_func_array($function, array_merge($arguments, func_get_args()));
    };
}

################################## User methods

function createMonthCost(callable $next, $day, $needle, $percent) {
    return partial('criteriaCost', $next, function () use ($day, $needle) {
        return $day == $needle;
    }, $percent);
}

function createBirthdayCost(callable $next, $day, $needle, $percent) {
    return partial('criteriaCost', $next, function () use ($day, $needle) {
        return $day == $needle;
    }, $percent);
}

function createNewYearCost(callable $next, $month, $day, $percent) {
    return partial('criteriaCost', $next, function () use ($month, $day) {
        return $month == 12 && $day > 20;
    }, $percent);
}

function createBigCost(callable $next, $limit, $percent) {
    return partial('criteriaCost', $next, function ($cost) use ($limit) {
        return $cost >= $limit;
    }, $percent);
}

################################## Infrastructure

$itemCost = function ($item) { return $item['price'] * $item['count']; };
$itemPrice = function ($item) { return $item['price']; };

################################## Initialization

$partialSimpleCost = partial('simpleCost', $itemCost);
$partialPriceBetweenCost = partial('priceBetweenCost', $partialSimpleCost, $itemCost, $itemPrice, 100, 150, 9);

$memoizedPriceBetweenCost = memoize($partialPriceBetweenCost);

$monthCost = createMonthCost($memoizedPriceBetweenCost, date('d'), 15, 5);
$birthdayCost = createBirthdayCost($memoizedPriceBetweenCost, date('m-d'), '08-12', 6);
$newYearCost = createNewYearCost($memoizedPriceBetweenCost, date('m'), date('d'), 7);

$partialMinCost = partial('minCost', [$monthCost, $birthdayCost, $newYearCost]);
$cost = createBigCost($partialMinCost, 1000, 7);

################################## Calculation

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

echo $cost($items) . PHP_EOL;

##################################

$times = [
    time(),
];

// --------------------

echo implode(', ', array_map(function ($item) { return date('Y-m-d', $item); }, $times));

// --------------------

$format = function ($item) { return date('Y-m-d', $item); };
echo implode(', ', array_map($format, $times));

// --------------------

$format = partial('date', 'Y-m-d');
echo implode(', ', array_map($format, $times));

// --------------------

echo implode(', ', array_map(partial('date', 'Y-m-d'), $times));

// --------------------