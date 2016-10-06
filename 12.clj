
(defn simple-cost [items]
    (reduce +
        (map #(* (get % :price) (get % :count)) items)))

(defn create-price-between-cost [next min max percent]
    (fn [items]
        (-
            (next items)
            (reduce +
                (map #(* (* (/ percent 100) (:price %)) (:count %))
                    (filter #(and (<= min (:price %)) (<= (:price %) max)) items))))))

(defn create-month-cost [next day needle percent]
    (fn [items]
        (if (= day needle)
            (* (- 1 (/ percent 100)) (next items))
            (next items))))

(defn create-big-cost [next limit percent]
    (fn [items]
        (let [cost (next items)]
            (if (>= cost limit)
                (* (- 1 (/ percent 100)) cost)
                cost))))

(def day (. (java.time.LocalDate/now) getDayOfMonth))

(def price-between-cost (create-price-between-cost simple-cost 100 150 10))
(def month-cost (create-month-cost price-between-cost day 15 5))
(def cost (create-big-cost month-cost 1000 7))

(def items [
    {:count 2 :price 75}
    {:count 5 :price 150}
])

(println (str (int (cost items))))

;; java -cp clojure.jar clojure.main 12.clj