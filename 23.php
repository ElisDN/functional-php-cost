<?php

// Object-functions alternative

################################## Framework

class SimpleCost
{
    private $itemCost;

    function __construct(callable $itemCost) {
        $this->itemCost = $itemCost;
    }

    function __invoke(array $items) {
        return array_sum(
            array_map($this->itemCost, $items));
    }
}

class PriceBetweenCost
{
    private $next;
    private $min;
    private $max;
    private $percent;
    private $itemCost;
    private $itemPrice;

    function __construct(callable $next, $min, $max, $percent, callable $itemCost, callable $itemPrice) {
        $this->next = $next;
        $this->min = $min;
        $this->max = $max;
        $this->percent = $percent;
        $this->itemCost = $itemCost;
        $this->itemPrice = $itemPrice;
    }

    function __invoke(array $items) {
        return call_user_func($this->next, $items) - array_sum(
            array_map([$this, 'discount'],
                array_filter($items, [$this, 'filter'])));
    }

    private function discount($item) {
        return ($this->percent / 100) * call_user_func($this->itemCost, $item);
    }

    private function filter($item) {
        $price = call_user_func($this->itemPrice, $item);
        return $this->min <= $price && $price <= $this->max;
    }
}

class CriteriaCost
{
    private $next;
    private $criteria;
    private $percent;

    function __construct(callable $next, callable $criteria, $percent) {
        $this->next = $next;
        $this->criteria = $criteria;
        $this->percent = $percent;
    }

    function __invoke(array $items) {
        $cost = call_user_func($this->next, $items);
        return call_user_func($this->criteria, $cost) ? (1 - $this->percent / 100) * $cost : $cost;
    }
}

class MinCost
{
    private $costs;

    function __construct(array $costs) {
        $this->costs = $costs;
    }

    function __invoke(array $items) {
        return min(
            array_map(function ($cost) use ($items) { return $cost($items); }, $this->costs));
    }
}

################################## Tools

class Memoize
{
    private $func;
    private $memory = [];

    function __construct(callable $func) {
        $this->func = $func;
    }

    function __invoke() {
        $args = func_get_args();
        $key = md5(serialize($args));

        if (!array_key_exists($key, $this->memory)) {
            $this->memory[$key] = call_user_func_array($this->func, $args);
        }

        return $this->memory[$key];
    }
}

################################## User methods

class MonthCost
{
    private $cost;

    function __construct(callable $next, $day, $needle, $percent) {
        $this->cost = new CriteriaCost($next, function () use ($day, $needle) {
            return $day == $needle;
        }, $percent);
    }

    function __invoke(array $items) {
        return call_user_func($this->cost, $items);
    }
}

class BirthdayCost
{
    private $cost;

    function __construct(callable $next, $day, $needle, $percent) {
        $this->cost = new CriteriaCost($next, function () use ($day, $needle) {
            return $day == $needle;
        }, $percent);
    }

    function __invoke(array $items) {
        return call_user_func($this->cost, $items);
    }
}

class NewYearCost
{
    private $cost;

    function __construct(callable $next, $month, $day, $percent) {
        $this->cost = new CriteriaCost($next, function () use ($month, $day) {
            return $month == 12 && $day > 20;
        }, $percent);
    }

    function __invoke(array $items) {
        return call_user_func($this->cost, $items);
    }
}

class BigCost
{
    private $cost;

    function __construct(callable $next, $limit, $percent) {
        $this->cost = new CriteriaCost($next, function ($cost) use ($limit) {
            return $cost >= $limit;
        }, $percent);
    }

    function __invoke(array $items) {
        return call_user_func($this->cost, $items);
    }
}

################################## Infrastructure

class Item
{
    private $count;
    private $price;

    function __construct($count, $price) {
        $this->count = $count;
        $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getCost() {
        return $this->price * $this->count;
    }
}

$itemCost = function (Item $item) { return $item->getCost(); };
$itemPrice = function (Item $item) { return $item->getPrice(); };

################################## Initialization

$simpleCost = new SimpleCost($itemCost);
$priceBetweenCost = new PriceBetweenCost($simpleCost, 100, 150, 9, $itemCost, $itemPrice);

$memoizedPriceBetweenCost = new Memoize($priceBetweenCost);

$monthCost = new MonthCost($memoizedPriceBetweenCost, date('d'), 15, 5);
$birthdayCost = new BirthdayCost($memoizedPriceBetweenCost, date('m-d'), '08-12', 6);
$newYearCost = new NewYearCost($memoizedPriceBetweenCost, date('m'), date('d'), 7);

$minCost = new MinCost([$monthCost, $birthdayCost, $newYearCost]);
$cost = new BigCost($minCost, 1000, 7);

################################## Calculation

$items = [
    new Item(2, 75),
    new Item(5, 150),
];

echo $cost($items) . PHP_EOL;