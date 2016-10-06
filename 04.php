<?php

function simpleCost(array $items) {
    $cost = 0;
    foreach ($items as $item) {
        $cost += $item['price'] * $item['count'];
    }
    return $cost;
}

$min = 100;
$max = 150;
$percent = 9;

function priceBetweenCost($cost, array $items) {
    global $min, $max, $percent;
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
$cost = priceBetweenCost($cost, $items);
$cost = monthCost($cost, date('d'), 15, 5);
$cost = bigCost($cost, 1000, 3);
echo $cost . PHP_EOL;

echo bigCost(monthCost(priceBetweenCost(simpleCost($items), $items), date('d'), 15, 5), 1000, 3) . PHP_EOL;
