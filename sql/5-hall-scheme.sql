-- Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

EXPLAIN ANALYZE
SELECT
    s.id, s.row, s.seat_number,
    sp.price,
    CASE
        WHEN o.status = 'paid'
            THEN 'Занято'
            ELSE 'Свободно'
    END AS seat_status
FROM
    seats s
        JOIN halls h ON h.id = s.hall_id
        JOIN sessions s2 on s2.hall_id = h.id
        JOIN seat_prices sp ON sp.seat_category_id = s.seat_category_id and sp.session_id = s2.id
        LEFT JOIN tickets t ON t.session_id = s2.id AND t.seat_id = s.id
        LEFT JOIN orders o ON o.id = t.order_id
WHERE
    s2.id = 70
ORDER BY
    s.row,
    s.seat_number;

/*
10k records
QUERY PLAN                                                                                                                                                   |
-------------------------------------------------------------------------------------------------------------------------------------------------------------+
Sort  (cost=606.53..606.71 rows=75 width=49) (actual time=2.897..2.901 rows=29 loops=1)                                                                      |
  Sort Key: s."row", s.seat_number                                                                                                                           |
  Sort Method: quicksort  Memory: 26kB                                                                                                                       |
  ->  Hash Join  (cost=12.85..604.19 rows=75 width=49) (actual time=1.061..2.885 rows=29 loops=1)                                                            |
        Hash Cond: (s.hall_id = h.id)                                                                                                                        |
        ->  Nested Loop Left Join  (cost=8.60..599.54 rows=75 width=29) (actual time=0.967..2.783 rows=29 loops=1)                                           |
              Join Filter: (t.seat_id = s.id)                                                                                                                |
              Rows Removed by Join Filter: 58                                                                                                                |
              ->  Nested Loop  (cost=8.31..391.68 rows=75 width=29) (actual time=0.252..2.036 rows=29 loops=1)                                               |
                    Join Filter: (s.seat_category_id = sp.seat_category_id)                                                                                  |
                    Rows Removed by Join Filter: 74                                                                                                          |
                    ->  Hash Join  (cost=8.31..199.68 rows=100 width=28) (actual time=0.041..1.322 rows=103 loops=1)                                         |
                          Hash Cond: (s.hall_id = s2.hall_id)                                                                                                |
                          ->  Seq Scan on seats s  (cost=0.00..164.00 rows=10000 width=20) (actual time=0.011..0.577 rows=10000 loops=1)                     |
                          ->  Hash  (cost=8.30..8.30 rows=1 width=8) (actual time=0.015..0.015 rows=1 loops=1)                                               |
                                Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                 |
                                ->  Index Scan using sessions_pkey on sessions s2  (cost=0.29..8.30 rows=1 width=8) (actual time=0.011..0.012 rows=1 loops=1)|
                                      Index Cond: (id = 70)                                                                                                  |
                    ->  Materialize  (cost=0.00..189.01 rows=2 width=13) (actual time=0.002..0.007 rows=1 loops=103)                                         |
                          ->  Seq Scan on seat_prices sp  (cost=0.00..189.00 rows=2 width=13) (actual time=0.207..0.680 rows=1 loops=1)                      |
                                Filter: (session_id = 70)                                                                                                    |
                                Rows Removed by Filter: 9999                                                                                                 |
              ->  Materialize  (cost=0.29..205.62 rows=2 width=12) (actual time=0.012..0.025 rows=2 loops=29)                                                |
                    ->  Nested Loop Left Join  (cost=0.29..205.61 rows=2 width=12) (actual time=0.335..0.710 rows=2 loops=1)                                 |
                          ->  Seq Scan on tickets t  (cost=0.00..189.00 rows=2 width=12) (actual time=0.325..0.697 rows=2 loops=1)                           |
                                Filter: (session_id = 70)                                                                                                    |
                                Rows Removed by Filter: 9998                                                                                                 |
                          ->  Index Scan using orders_pkey on orders o  (cost=0.29..8.30 rows=1 width=8) (actual time=0.005..0.005 rows=1 loops=2)           |
                                Index Cond: (id = t.order_id)                                                                                                |
        ->  Hash  (cost=3.00..3.00 rows=100 width=4) (actual time=0.081..0.081 rows=100 loops=1)                                                             |
              Buckets: 1024  Batches: 1  Memory Usage: 12kB                                                                                                  |
              ->  Seq Scan on halls h  (cost=0.00..3.00 rows=100 width=4) (actual time=0.041..0.048 rows=100 loops=1)                                        |
Planning Time: 0.726 ms                                                                                                                                      |
Execution Time: 3.008 ms                                                                                                                                     |                                                                                                                                  |                                                                                                                                   |
*/

/*
10kk records without indexes
QUERY PLAN                                                                                                                                                         |
-------------------------------------------------------------------------------------------------------------------------------------------------------------------+
Gather Merge  (cost=174969.82..175006.45 rows=314 width=49) (actual time=880.260..886.624 rows=379 loops=1)                                                        |
  Workers Planned: 2                                                                                                                                               |
  Workers Launched: 2                                                                                                                                              |
  ->  Sort  (cost=173969.79..173970.18 rows=157 width=49) (actual time=845.071..845.084 rows=126 loops=3)                                                          |
        Sort Key: s."row", s.seat_number                                                                                                                           |
        Sort Method: quicksort  Memory: 32kB                                                                                                                       |
        Worker 0:  Sort Method: quicksort  Memory: 32kB                                                                                                            |
        Worker 1:  Sort Method: quicksort  Memory: 33kB                                                                                                            |
        ->  Nested Loop  (cost=115796.57..173964.07 rows=157 width=49) (actual time=494.681..844.891 rows=126 loops=3)                                             |
              Join Filter: (h.id = s.hall_id)                                                                                                                      |
              ->  Parallel Hash Left Join  (cost=115796.27..173950.81 rows=156 width=29) (actual time=494.559..844.395 rows=126 loops=3)                           |
                    Hash Cond: (s.id = t.seat_id)                                                                                                                  |
                    ->  Parallel Hash Join  (cost=57898.14..116052.09 rows=156 width=29) (actual time=255.138..604.885 rows=126 loops=3)                           |
                          Hash Cond: (s.seat_category_id = sp.seat_category_id)                                                                                    |
                          ->  Hash Join  (cost=8.46..58160.85 rows=208 width=28) (actual time=4.986..354.870 rows=165 loops=3)                                     |
                                Hash Cond: (s.hall_id = s2.hall_id)                                                                                                |
                                ->  Parallel Seq Scan on seats s  (cost=0.00..52681.33 rows=2083333 width=20) (actual time=0.132..220.721 rows=1666667 loops=3)    |
                                ->  Hash  (cost=8.45..8.45 rows=1 width=8) (actual time=0.057..0.058 rows=1 loops=3)                                               |
                                      Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                 |
                                      ->  Index Scan using sessions_pkey on sessions s2  (cost=0.43..8.45 rows=1 width=8) (actual time=0.047..0.048 rows=1 loops=3)|
                                            Index Cond: (id = 70)                                                                                                  |
                          ->  Parallel Hash  (cost=57889.67..57889.67 rows=1 width=13) (actual time=249.813..249.815 rows=1 loops=3)                               |
                                Buckets: 1024  Batches: 1  Memory Usage: 40kB                                                                                      |
                                ->  Parallel Seq Scan on seat_prices sp  (cost=0.00..57889.67 rows=1 width=13) (actual time=227.567..249.762 rows=1 loops=3)       |
                                      Filter: (session_id = 70)                                                                                                    |
                                      Rows Removed by Filter: 1666666                                                                                              |
                    ->  Parallel Hash  (cost=57898.12..57898.12 rows=1 width=12) (actual time=239.324..239.325 rows=0 loops=3)                                     |
                          Buckets: 1024  Batches: 1  Memory Usage: 40kB                                                                                            |
                          ->  Nested Loop Left Join  (cost=0.43..57898.12 rows=1 width=12) (actual time=184.402..239.256 rows=0 loops=3)                           |
                                ->  Parallel Seq Scan on tickets t  (cost=0.00..57889.67 rows=1 width=12) (actual time=184.381..239.234 rows=0 loops=3)            |
                                      Filter: (session_id = 70)                                                                                                    |
                                      Rows Removed by Filter: 1666666                                                                                              |
                                ->  Index Scan using orders_pkey on orders o  (cost=0.43..8.45 rows=1 width=8) (actual time=0.057..0.057 rows=1 loops=1)           |
                                      Index Cond: (id = t.order_id)                                                                                                |
              ->  Memoize  (cost=0.30..4.31 rows=1 width=4) (actual time=0.002..0.002 rows=1 loops=379)                                                            |
                    Cache Key: s2.hall_id                                                                                                                          |
                    Cache Mode: logical                                                                                                                            |
                    Hits: 120  Misses: 1  Evictions: 0  Overflows: 0  Memory Usage: 1kB                                                                            |
                    Worker 0:  Hits: 122  Misses: 1  Evictions: 0  Overflows: 0  Memory Usage: 1kB                                                                 |
                    Worker 1:  Hits: 134  Misses: 1  Evictions: 0  Overflows: 0  Memory Usage: 1kB                                                                 |
                    ->  Index Only Scan using halls_pkey on halls h  (cost=0.29..4.30 rows=1 width=4) (actual time=0.115..0.115 rows=1 loops=3)                    |
                          Index Cond: (id = s2.hall_id)                                                                                                            |
                          Heap Fetches: 0                                                                                                                          |
Planning Time: 0.926 ms                                                                                                                                            |
Execution Time: 886.754 ms                                                                                                                                         |                                                                                                                                       |                                                                                                                                        |                                                                                                                                      |
*/

/*
10kk records with indexes
QUERY PLAN                                                                                                                                                           |
---------------------------------------------------------------------------------------------------------------------------------------------------------------------+
Sort  (cost=108.52..109.46 rows=377 width=49) (actual time=0.718..0.733 rows=379 loops=1)                                                                            |
  Sort Key: s."row", s.seat_number                                                                                                                                   |
  Sort Method: quicksort  Memory: 48kB                                                                                                                               |
  ->  Hash Left Join  (cost=43.03..92.39 rows=377 width=49) (actual time=0.076..0.593 rows=379 loops=1)                                                              |
        Hash Cond: (s.id = t.seat_id)                                                                                                                                |
        ->  Hash Join  (cost=13.64..60.16 rows=377 width=21) (actual time=0.057..0.527 rows=379 loops=1)                                                             |
              Hash Cond: (s.seat_category_id = sp.seat_category_id)                                                                                                  |
              ->  Nested Loop  (cost=1.15..41.39 rows=500 width=20) (actual time=0.034..0.434 rows=495 loops=1)                                                      |
                    ->  Nested Loop  (cost=0.72..12.75 rows=1 width=12) (actual time=0.022..0.023 rows=1 loops=1)                                                    |
                          ->  Index Scan using sessions_pkey on sessions s2  (cost=0.43..8.45 rows=1 width=8) (actual time=0.012..0.012 rows=1 loops=1)              |
                                Index Cond: (id = 70)                                                                                                                |
                          ->  Index Only Scan using halls_pkey on halls h  (cost=0.29..4.30 rows=1 width=4) (actual time=0.009..0.009 rows=1 loops=1)                |
                                Index Cond: (id = s2.hall_id)                                                                                                        |
                                Heap Fetches: 0                                                                                                                      |
                    ->  Index Scan using "idx-seats-hall_id" on seats s  (cost=0.43..23.66 rows=498 width=20) (actual time=0.011..0.322 rows=495 loops=1)            |
                          Index Cond: (hall_id = h.id)                                                                                                               |
              ->  Hash  (cost=12.47..12.47 rows=2 width=13) (actual time=0.014..0.015 rows=2 loops=1)                                                                |
                    Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                                     |
                    ->  Index Scan using "idx-seat_prices-session_id" on seat_prices sp  (cost=0.43..12.47 rows=2 width=13) (actual time=0.009..0.010 rows=2 loops=1)|
                          Index Cond: (session_id = 70)                                                                                                              |
        ->  Hash  (cost=29.37..29.37 rows=2 width=12) (actual time=0.012..0.013 rows=1 loops=1)                                                                      |
              Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                                           |
              ->  Nested Loop Left Join  (cost=0.86..29.37 rows=2 width=12) (actual time=0.009..0.010 rows=1 loops=1)                                                |
                    ->  Index Scan using "idx-tickets-session_id" on tickets t  (cost=0.43..12.47 rows=2 width=12) (actual time=0.005..0.005 rows=1 loops=1)         |
                          Index Cond: (session_id = 70)                                                                                                              |
                    ->  Index Scan using orders_pkey on orders o  (cost=0.43..8.45 rows=1 width=8) (actual time=0.004..0.004 rows=1 loops=1)                         |
                          Index Cond: (id = t.order_id)                                                                                                              |
Planning Time: 1.318 ms                                                                                                                                              |
Execution Time: 0.829 ms                                                                                                                                             |                                                                                                                                         |
*/
