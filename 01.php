<?php

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

$cost = 0;
foreach ($items as $item) {
    $cost += $item['price'] * $item['count'];
}

foreach ($items as $item) {
    if (100 <= $item['price'] && $item['price'] <= 150) {
        $cost -= (9 / 100) * $item['price'] * $item['count'];
    }
}

if (date('d') == 15) {
    $cost = 0.95 * $cost;
}

if ($cost >= 1000) {
    $cost = 0.93 * $cost;
}

echo $cost . PHP_EOL;