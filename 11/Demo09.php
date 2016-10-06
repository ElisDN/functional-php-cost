<?php

$items = [
    ['count' => 2, 'price' => 75],
    ['count' => 5, 'price' => 150],
];

reduce(
    function ($total, $current) { return $total + $current; },
    map(
        function ($item) { return 0.95 * $item['price'] * $item['count']; },
        filter(
            function ($item) { return 100 <= $item['price'] && $item['price'] <= 150; },
            $items)));


$sum = function ($total, $current) { return $total + $current; };
$discount = function ($item) { return 0.05 * $item['price'] * $item['count']; };
$filter = function ($item) { return 100 <= $item['price'] && $item['price'] <= 150; };

echo reduce($sum, // сумма
        map($discount, // скидки
            filter($filter, $items))); // отфильтрованных товаров

/*
(reduce +
    (map #(* 0.05 (:price %) (:count %))
        (filter #(and (<= 100 (:price %)) (<= (:price %) 150)) items)))
*/