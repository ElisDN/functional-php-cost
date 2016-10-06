<?php

// Static functions alternative

################################## Framework

class Cost
{
    static function createSimple(callable $itemCost)
    {
        return function (array $items) use ($itemCost) {
            return array_sum(
                array_map($itemCost, $items));
        };
    }

    static function createPriceBetween(callable $next, $min, $max, $percent, callable $itemCost, callable $itemPrice)
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

    static function createCriteria(callable $next, callable $criteria, $percent)
    {
        return function (array $items) use ($next, $criteria, $percent) {
            $cost = $next($items);
            return $criteria($cost) ? (1 - $percent / 100) * $cost : $cost;
        };
    }

    static function createMin(array $costs)
    {
        return function (array $items) use ($costs) {
            return min(
                array_map(function ($cost) use ($items) { return $cost($items); }, $costs));
        };
    }
}

################################## Tools

class Tools
{
    static function memoize(callable $func)
    {
        return function () use ($func) {
            static $memory = [];

            $args = func_get_args();
            $key = md5(serialize($args));

            if (!array_key_exists($key, $memory)) {
                $memory[$key] = call_user_func_array($func, $args);
            }

            return $memory[$key];
        };
    }
}

################################## User methods

class UserCost
{
    static function createMonth(callable $next, $day, $needle, $percent) {
        return Cost::createCriteria($next, function () use ($day, $needle) {
            return $day == $needle;
        }, $percent);
    }

    static function createBirthday(callable $next, $day, $needle, $percent) {
        return Cost::createCriteria($next, function () use ($day, $needle) {
            return $day == $needle;
        }, $percent);
    }

    static function createNewYear(callable $next, $month, $day, $percent) {
        return Cost::createCriteria($next, function () use ($month, $day) {
            return $month == 12 && $day > 20;
        }, $percent);
    }

    static function createBig(callable $next, $limit, $percent) {
        return Cost::createCriteria($next, function ($cost) use ($limit) {
            return $cost >= $limit;
        }, $percent);
    }
}

################################## Infrastructure

$itemCost = function ($item) { return $item['price'] * $item['count']; };
$itemPrice = function ($item) { return $item['price']; };

################################## Initialization

$simpleCost = Cost::createSimple($itemCost);
$priceBetweenCost = Cost::createPriceBetween($simpleCost, 100, 150, 9, $itemCost, $itemPrice);

$memoizedPriceBetweenCost = Tools::memoize($priceBetweenCost);

$monthCost = UserCost::createMonth($memoizedPriceBetweenCost, date('d'), 15, 5);
$birthdayCost = UserCost::createBirthday($memoizedPriceBetweenCost, date('m-d'), '08-12', 6);
$newYearCost = UserCost::createNewYear($memoizedPriceBetweenCost, date('m'), date('d'), 7);

$minCost = Cost::createMin([$monthCost, $birthdayCost, $newYearCost]);
$cost = UserCost::createBig($minCost, 1000, 7);

################################## Calculation

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

echo $cost($items) . PHP_EOL;