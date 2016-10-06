<?php

function simpleCost(array $items) {
    $cost = 0;
    foreach ($items as $item) {
        $cost += $item['price'] * $item['count'];
    }
    return $cost;
}

function createPriceBetweenCost($min, $max, $percent) {
    return function ($cost, array $items) use ($min, $max, $percent) {
        $discount = 0;
        foreach ($items as $item) {
            if ($min <= $item['price'] && $item['price'] <= $max) {
                $discount += ($percent / 100) * $item['price'] * $item['count'];
            }
        }
        return $cost - $discount;
    };
}

function createMonthCost($day, $needle, $percent) {
    return function ($cost, array $items) use ($day, $needle, $percent) {
        return ($day == $needle) ? (1 - $percent / 100) * $cost : $cost;
    };
}

function createBigCost($limit, $percent) {
    return function ($cost, array $items) use ($limit, $percent) {
        if ($cost >= $limit) {
            return (1 - $percent / 100) * $cost;
        } else {
            return $cost;
        }
    };
}

##################################

$priceBetweenCostFunction = createPriceBetweenCost(100, 150, 9);
$monthCostFunction = createMonthCost(date('d'), 15, 5);
$bigCostFunction = createBigCost(1000, 7);

##################################

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

$cost = simpleCost($items);
$cost = $priceBetweenCostFunction($cost, $items);
$cost = $monthCostFunction($cost, $items);
$cost = $bigCostFunction($cost, $items);
echo $cost . PHP_EOL;