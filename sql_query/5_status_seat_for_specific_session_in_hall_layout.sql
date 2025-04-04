-- Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

EXPLAIN ANALYZE
SELECT places.id,
       places.row,
       places.number,
       price_list.price,
       CASE
           WHEN orders.status = 'reserved'
               THEN 'Зарезервировано'
           WHEN orders.status = 'paid'
               THEN 'Занято'
           ELSE 'Свободно'
           END AS seat_status
FROM places
    JOIN halls
        ON halls.id = places.hall_id
    JOIN sessions
        ON sessions.hall_id = halls.id
    JOIN price_list
        ON price_list.place_category_id = places.place_category_id
            AND price_list.session_id = sessions.id
    LEFT JOIN tickets
        ON tickets.session_id = sessions.id
            AND tickets.place_id = places.id
    LEFT JOIN orders
        ON orders.id = tickets.order_id
WHERE sessions.id = 55
ORDER BY
    places.row,
    places.number;

/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000 строк
------------------------------------------------------------------------------------------------------------------------
Sort  (cost=588.37..588.37 rows=1 width=49) (actual time=1.159..1.163 rows=0 loops=1)
"  Sort Key: places.""row"", places.number"
  Sort Method: quicksort  Memory: 25kB
  ->  Nested Loop Left Join  (cost=197.77..588.36 rows=1 width=49) (actual time=1.156..1.159 rows=0 loops=1)
        ->  Nested Loop Left Join  (cost=197.49..580.05 rows=1 width=21) (actual time=1.155..1.158 rows=0 loops=1)
              Join Filter: (tickets.place_id = places.id)
              ->  Nested Loop  (cost=197.49..391.02 rows=1 width=21) (actual time=1.155..1.158 rows=0 loops=1)
                    ->  Hash Join  (cost=197.34..390.86 rows=1 width=29) (actual time=1.155..1.157 rows=0 loops=1)
                          Hash Cond: (places.place_category_id = price_list.place_category_id)
                          ->  Hash Join  (cost=8.31..200.81 rows=202 width=28) (actual time=0.021..0.844 rows=198 loops=1)
                                Hash Cond: (places.hall_id = sessions.hall_id)
                                ->  Seq Scan on places  (cost=0.00..164.00 rows=10000 width=20) (actual time=0.004..0.359 rows=10000 loops=1)
                                ->  Hash  (cost=8.30..8.30 rows=1 width=8) (actual time=0.008..0.009 rows=1 loops=1)
                                      Buckets: 1024  Batches: 1  Memory Usage: 9kB
                                      ->  Index Scan using sessions_pkey on sessions  (cost=0.29..8.30 rows=1 width=8) (actual time=0.006..0.007 rows=1 loops=1)
                                            Index Cond: (id = 55)
                          ->  Hash  (cost=189.00..189.00 rows=2 width=13) (actual time=0.302..0.302 rows=1 loops=1)
                                Buckets: 1024  Batches: 1  Memory Usage: 9kB
                                ->  Seq Scan on price_list  (cost=0.00..189.00 rows=2 width=13) (actual time=0.011..0.301 rows=1 loops=1)
                                      Filter: (session_id = 55)
                                      Rows Removed by Filter: 9999
                    ->  Index Only Scan using halls_pkey on halls  (cost=0.14..0.17 rows=1 width=4) (never executed)
                          Index Cond: (id = places.hall_id)
                          Heap Fetches: 0
              ->  Seq Scan on tickets  (cost=0.00..189.00 rows=2 width=12) (never executed)
                    Filter: (session_id = 55)
        ->  Index Scan using orders_pkey on orders  (cost=0.29..8.30 rows=1 width=8) (never executed)
              Index Cond: (id = tickets.order_id)
Planning Time: 0.502 ms
Execution Time: 1.197 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк
------------------------------------------------------------------------------------------------------------------------
Nested Loop Left Join  (cost=234430.21..350331.97 rows=1 width=49) (actual time=1020.692..1023.776 rows=0 loops=1)
  ->  Nested Loop Left Join  (cost=234429.77..350323.52 rows=1 width=21) (actual time=1020.691..1023.775 rows=0 loops=1)
        Join Filter: (tickets.place_id = places.id)
        ->  Nested Loop  (cost=233429.77..233429.91 rows=1 width=21) (actual time=1020.690..1023.774 rows=0 loops=1)
              ->  Gather Merge  (cost=233429.50..233429.62 rows=1 width=29) (actual time=1020.689..1023.772 rows=0 loops=1)
                    Workers Planned: 2
                    Workers Launched: 2
                    ->  Sort  (cost=232429.48..232429.48 rows=1 width=29) (actual time=996.767..996.771 rows=0 loops=3)
"                          Sort Key: places.""row"", places.number"
                          Sort Method: quicksort  Memory: 25kB
                          Worker 0:  Sort Method: quicksort  Memory: 25kB
                          Worker 1:  Sort Method: quicksort  Memory: 25kB
                          ->  Parallel Hash Join  (cost=115901.93..232429.47 rows=1 width=29) (actual time=996.593..996.596 rows=0 loops=3)
                                Hash Cond: (places.place_category_id = price_list.place_category_id)
                                ->  Hash Join  (cost=8.46..116507.56 rows=7583 width=28) (actual time=1.529..570.064 rows=6100 loops=3)
                                      Hash Cond: (places.hall_id = sessions.hall_id)
                                      ->  Parallel Seq Scan on places  (cost=0.00..105466.30 rows=4170830 width=20) (actual time=0.365..410.893 rows=3336667 loops=3)
                                      ->  Hash  (cost=8.45..8.45 rows=1 width=8) (actual time=0.965..0.966 rows=1 loops=3)
                                            Buckets: 1024  Batches: 1  Memory Usage: 9kB
                                            ->  Index Scan using sessions_pkey on sessions  (cost=0.43..8.45 rows=1 width=8) (actual time=0.960..0.961 rows=1 loops=3)
                                                  Index Cond: (id = 55)
                                ->  Parallel Hash  (cost=115893.45..115893.45 rows=1 width=13) (actual time=425.323..425.323 rows=0 loops=3)
                                      Buckets: 1024  Batches: 1  Memory Usage: 40kB
                                      ->  Parallel Seq Scan on price_list  (cost=0.00..115893.45 rows=1 width=13) (actual time=282.330..425.191 rows=0 loops=3)
                                            Filter: (session_id = 55)
                                            Rows Removed by Filter: 3336666
              ->  Index Only Scan using halls_pkey on halls  (cost=0.28..0.29 rows=1 width=4) (never executed)
                    Index Cond: (id = places.hall_id)
                    Heap Fetches: 0
        ->  Gather  (cost=1000.00..116893.58 rows=2 width=12) (never executed)
              Workers Planned: 2
              Workers Launched: 0
              ->  Parallel Seq Scan on tickets  (cost=0.00..115893.38 rows=1 width=12) (never executed)
                    Filter: (session_id = 55)
  ->  Index Scan using orders_pkey on orders  (cost=0.43..8.45 rows=1 width=8) (never executed)
        Index Cond: (id = tickets.order_id)
Planning Time: 5.640 ms
JIT:
  Functions: 83
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 5.143 ms (Deform 1.038 ms), Inlining 0.000 ms, Optimization 2.900 ms, Emission 36.721 ms, Total 44.764 ms"
Execution Time: 1024.753 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк после индексации
------------------------------------------------------------------------------------------------------------------------
Sort  (cost=1136.06..1136.07 rows=1 width=49) (actual time=22.535..22.549 rows=0 loops=1)
"  Sort Key: places.""row"", places.number"
  Sort Method: quicksort  Memory: 25kB
  ->  Nested Loop Left Join  (cost=14.51..1136.05 rows=1 width=49) (actual time=22.524..22.538 rows=0 loops=1)
        ->  Nested Loop Left Join  (cost=14.07..1127.60 rows=1 width=21) (actual time=22.523..22.536 rows=0 loops=1)
              ->  Hash Join  (cost=13.64..1127.08 rows=1 width=21) (actual time=22.521..22.533 rows=0 loops=1)
                    Hash Cond: (places.place_category_id = price_list.place_category_id)
                    ->  Nested Loop  (cost=1.15..1023.57 rows=18200 width=20) (actual time=0.029..21.693 rows=18300 loops=1)
                          ->  Nested Loop  (cost=0.71..16.77 rows=1 width=12) (actual time=0.019..0.028 rows=1 loops=1)
                                ->  Index Scan using sessions_pkey on sessions  (cost=0.43..8.45 rows=1 width=8) (actual time=0.011..0.015 rows=1 loops=1)
                                      Index Cond: (id = 55)
                                ->  Index Only Scan using halls_pkey on halls  (cost=0.28..8.29 rows=1 width=4) (actual time=0.003..0.005 rows=1 loops=1)
                                      Index Cond: (id = sessions.hall_id)
                                      Heap Fetches: 1
"                          ->  Index Scan using ""idx-places-hall_id"" on places  (cost=0.43..824.80 rows=18200 width=20) (actual time=0.009..20.225 rows=18300 loops=1)"
                                Index Cond: (hall_id = halls.id)
                    ->  Hash  (cost=12.47..12.47 rows=2 width=13) (actual time=0.016..0.021 rows=1 loops=1)
                          Buckets: 1024  Batches: 1  Memory Usage: 9kB
"                          ->  Index Scan using ""idx-price_list-session_id"" on price_list  (cost=0.43..12.47 rows=2 width=13) (actual time=0.011..0.012 rows=1 loops=1)"
                                Index Cond: (session_id = 55)
"              ->  Index Scan using ""idx-tickets-place_id"" on tickets  (cost=0.43..0.51 rows=1 width=12) (never executed)"
                    Index Cond: (place_id = places.id)
                    Filter: (session_id = 55)
        ->  Index Scan using orders_pkey on orders  (cost=0.43..8.45 rows=1 width=8) (never executed)
              Index Cond: (id = tickets.order_id)
Planning Time: 1.067 ms
Execution Time: 22.631 ms
------------------------------------------------------------------------------------------------------------------------
*/
