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

$priceBetweenCost = function ($cost, array $items) use ($min, $max, $percent) {
    $discount = 0;
    foreach ($items as $item) {
        if ($min <= $item['price'] && $item['price'] <= $max) {
            $discount += ($percent / 100) * $item['price'] * $item['count'];
        }
    }
    return $cost - $discount;
};

$day = date('d');
$needle = 15;
$percent = 5;

$monthCost = function ($cost) use ($day, $needle, $percent) {
    return ($day == $needle) ? (1 - $percent / 100) * $cost : $cost;
};

$limit = 1000;
$percent = 7;

$bigCost = function ($cost) use ($limit, $percent) {
    if ($cost >= $limit) {
        return (1 - $percent / 100) * $cost;
    } else {
        return $cost;
    }
};

##################################

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

$cost = simpleCost($items);
$cost = $priceBetweenCost($cost, $items);
$cost = $monthCost($cost);
$cost = $bigCost($cost);
echo $cost . PHP_EOL;

echo $bigCost($monthCost($priceBetweenCost(simpleCost($items), $items))) . PHP_EOL;