<?php

function simpleCost(array $items) {
    $cost = 0;
    foreach ($items as $item) {
        $cost += $item['price'] * $item['count'];
    }
    return $cost;
}

function priceBetweenCost($cost, array $items, $min, $max, $percent) {
    $discount = 0;
    foreach ($items as $item) {
        if ($min <= $item['price'] && $item['price'] <= $max) {
            $discount += ($percent / 100) * $item['price'] * $item['count'];
        }
    }
    return $cost - $discount;
}

function monthCost($cost, $day, $needle, $percent) {
    if ($day == $needle) {
        return (1 - $percent / 100) * $cost;
    } else {
        return $cost;
    }
}

function bigCost($cost, $limit, $percent) {
    if ($cost >= $limit) {
        return (1 - $percent / 100) * $cost;
    } else {
        return $cost;
    }
}

##################################

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];
$cost = simpleCost($items);
$cost = priceBetweenCost($cost, $items, 100, 150, 9);
$cost = monthCost($cost, date('d'), 15, 5);
$cost = bigCost($cost, 1000, 3);
echo $cost . PHP_EOL;

$items = [
    ['count' => 5, 'price' => 93],
    ['count' => 7, 'price' => 12],
];
$cost = simpleCost($items);
$cost = priceBetweenCost($cost, $items, 100, 150, 9);
$cost = monthCost($cost, date('d'), 15, 5);
$cost = bigCost($cost, 1000, 3);
echo $cost . PHP_EOL;