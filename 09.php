<?php

function createSimpleCost() {
    return function (array $items) {
        $cost = 0;
        foreach ($items as $item) {
            $cost += $item['price'] * $item['count'];
        }
        return $cost;
    };
}

function createPriceBetweenCost(callable $nextFunction, $min, $max, $percent) {
    return function (array $items) use ($nextFunction, $min, $max, $percent) {
        $discount = 0;
        foreach ($items as $item) {
            if ($min <= $item['price'] && $item['price'] <= $max) {
                $discount += ($percent / 100) * $item['price'] * $item['count'];
            }
        }
        return $nextFunction($items) - $discount;
    };
}

function createMonthCost(callable $nextFunction, $day, $needle, $percent) {
    return function (array $items) use ($nextFunction, $day, $needle, $percent) {
        return ($day == $needle) ? (1 - $percent / 100) * $nextFunction($items) : $nextFunction($items);
    };
}

function createBigCost(callable $nextFunction, $limit, $percent) {
    return function (array $items) use ($nextFunction, $limit, $percent) {
        $cost = $nextFunction($items);
        return ($cost >= $limit) ? (1 - $percent / 100) * $cost : $cost;
    };
}

##################################

$simpleCostFunction = createSimpleCost();
$priceBetweenCostFunction = createPriceBetweenCost($simpleCostFunction, 100, 150, 9);
$monthCostFunction = createMonthCost($priceBetweenCostFunction, date('d'), 15, 5);
$costFunction = createBigCost($monthCostFunction, 1000, 7);

##################################

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

echo $costFunction($items) . PHP_EOL;
