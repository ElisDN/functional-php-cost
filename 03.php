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
    return ($day == $needle) ? (1 - $percent / 100) * $cost : $cost;
}

function bigCost($cost, $limit, $percent) {
    return ($cost >= $limit) ? (1 - $percent / 100) * $cost : $cost;
}

##################################

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

echo bigCost(monthCost(priceBetweenCost(simpleCost($items), $items, 100, 150, 9), date('d'), 15, 5), 1000, 3) . PHP_EOL;
