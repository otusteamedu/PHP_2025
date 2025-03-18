-- Поиск 3 самых прибыльных фильмов за неделю

EXPLAIN ANALYZE
SELECT
    m.name,
    SUM(t.price) as profit
FROM
    movies m
        JOIN sessions s ON m.id = s.movie_id
        JOIN tickets t ON s.id = t.session_id
        JOIN orders o ON o.id = t.order_id
WHERE
    o.status = 'paid'
    AND DATE(o.paid_at) >= CURRENT_DATE - INTERVAL '1 week' AND DATE(o.paid_at) < CURRENT_DATE
GROUP BY
    m.id
ORDER BY
    profit DESC
LIMIT 3;

/*
10k records
QUERY PLAN                                                                                                                                                                               |
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
Limit  (cost=577.28..577.29 rows=3 width=87) (actual time=2.850..2.853 rows=3 loops=1)                                                                                                   |
  ->  Sort  (cost=577.28..577.33 rows=17 width=87) (actual time=2.849..2.851 rows=3 loops=1)                                                                                             |
        Sort Key: (sum(t.price)) DESC                                                                                                                                                    |
        Sort Method: top-N heapsort  Memory: 25kB                                                                                                                                        |
        ->  GroupAggregate  (cost=576.72..577.06 rows=17 width=87) (actual time=2.821..2.833 rows=18 loops=1)                                                                            |
              Group Key: m.id                                                                                                                                                            |
              ->  Sort  (cost=576.72..576.77 rows=17 width=60) (actual time=2.814..2.817 rows=18 loops=1)                                                                                |
                    Sort Key: m.id                                                                                                                                                       |
                    Sort Method: quicksort  Memory: 26kB                                                                                                                                 |
                    ->  Nested Loop  (cost=374.78..576.38 rows=17 width=60) (actual time=1.420..2.802 rows=18 loops=1)                                                                   |
                          ->  Nested Loop  (cost=374.50..570.26 rows=17 width=9) (actual time=1.411..2.748 rows=18 loops=1)                                                              |
                                ->  Hash Join  (cost=374.21..564.47 rows=17 width=9) (actual time=1.397..2.683 rows=18 loops=1)                                                          |
                                      Hash Cond: (t.order_id = o.id)                                                                                                                     |
                                      ->  Seq Scan on tickets t  (cost=0.00..164.00 rows=10000 width=13) (actual time=0.020..0.607 rows=10000 loops=1)                                   |
                                      ->  Hash  (cost=374.00..374.00 rows=17 width=4) (actual time=1.325..1.326 rows=23 loops=1)                                                         |
                                            Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                                 |
                                            ->  Seq Scan on orders o  (cost=0.00..374.00 rows=17 width=4) (actual time=0.040..1.318 rows=23 loops=1)                                     |
                                                  Filter: ((status = 'paid'::order_status) AND (date(paid_at) < CURRENT_DATE) AND (date(paid_at) >= (CURRENT_DATE - '7 days'::interval)))|
                                                  Rows Removed by Filter: 9977                                                                                                           |
                                ->  Index Scan using sessions_pkey on sessions s  (cost=0.29..0.34 rows=1 width=8) (actual time=0.003..0.003 rows=1 loops=18)                            |
                                      Index Cond: (id = t.session_id)                                                                                                                    |
                          ->  Index Scan using movies_pkey on movies m  (cost=0.29..0.36 rows=1 width=55) (actual time=0.002..0.002 rows=1 loops=18)                                     |
                                Index Cond: (id = s.movie_id)                                                                                                                            |
Planning Time: 0.790 ms                                                                                                                                                                  |
Execution Time: 2.904 ms                                                                                                                                                                 |                                                                                                                                                |                                                                                                                                             |
*/

/*
10kk records without indexes
QUERY PLAN                                                                                                                                                                                           |
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
Limit  (cost=163331.51..163331.52 rows=3 width=87) (actual time=1318.675..1328.685 rows=3 loops=1)                                                                                                   |
  ->  Sort  (cost=163331.51..163352.71 rows=8481 width=87) (actual time=1318.674..1328.683 rows=3 loops=1)                                                                                           |
        Sort Key: (sum(t.price)) DESC                                                                                                                                                                |
        Sort Method: top-N heapsort  Memory: 25kB                                                                                                                                                    |
        ->  Finalize GroupAggregate  (cost=162176.37..163221.90 rows=8481 width=87) (actual time=1296.656..1325.361 rows=16051 loops=1)                                                              |
              Group Key: m.id                                                                                                                                                                        |
              ->  Gather Merge  (cost=162176.37..163062.87 rows=7068 width=87) (actual time=1296.642..1314.274 rows=16083 loops=1)                                                                   |
                    Workers Planned: 2                                                                                                                                                               |
                    Workers Launched: 2                                                                                                                                                              |
                    ->  Partial GroupAggregate  (cost=161176.35..161247.03 rows=3534 width=87) (actual time=1261.097..1265.551 rows=5361 loops=3)                                                    |
                          Group Key: m.id                                                                                                                                                            |
                          ->  Sort  (cost=161176.35..161185.18 rows=3534 width=60) (actual time=1261.071..1261.817 rows=5366 loops=3)                                                                |
                                Sort Key: m.id                                                                                                                                                       |
                                Sort Method: quicksort  Memory: 666kB                                                                                                                                |
                                Worker 0:  Sort Method: quicksort  Memory: 647kB                                                                                                                     |
                                Worker 1:  Sort Method: quicksort  Memory: 648kB                                                                                                                     |
                                ->  Nested Loop  (cost=99310.04..160968.07 rows=3534 width=60) (actual time=404.231..1256.807 rows=5366 loops=3)                                                     |
                                      ->  Nested Loop  (cost=99309.61..159178.36 rows=3534 width=9) (actual time=404.055..1076.608 rows=5366 loops=3)                                                |
                                            ->  Parallel Hash Join  (cost=99309.18..157459.26 rows=3534 width=9) (actual time=403.833..887.847 rows=5366 loops=3)                                    |
                                                  Hash Cond: (t.order_id = o.id)                                                                                                                     |
                                                  ->  Parallel Seq Scan on tickets t  (cost=0.00..52681.33 rows=2083333 width=13) (actual time=0.119..227.290 rows=1666667 loops=3)                  |
                                                  ->  Parallel Hash  (cost=99265.00..99265.00 rows=3534 width=4) (actual time=403.288..403.289 rows=5351 loops=3)                                    |
                                                        Buckets: 16384  Batches: 1  Memory Usage: 832kB                                                                                              |
                                                        ->  Parallel Seq Scan on orders o  (cost=0.00..99265.00 rows=3534 width=4) (actual time=0.166..400.429 rows=5351 loops=3)                    |
                                                              Filter: ((status = 'paid'::order_status) AND (date(paid_at) < CURRENT_DATE) AND (date(paid_at) >= (CURRENT_DATE - '7 days'::interval)))|
                                                              Rows Removed by Filter: 1661316                                                                                                        |
                                            ->  Index Scan using sessions_pkey on sessions s  (cost=0.43..0.49 rows=1 width=8) (actual time=0.034..0.034 rows=1 loops=16098)                         |
                                                  Index Cond: (id = t.session_id)                                                                                                                    |
                                      ->  Index Scan using movies_pkey on movies m  (cost=0.43..0.51 rows=1 width=55) (actual time=0.033..0.033 rows=1 loops=16098)                                  |
                                            Index Cond: (id = s.movie_id)                                                                                                                            |
Planning Time: 1.006 ms                                                                                                                                                                              |
Execution Time: 1328.938 ms                                                                                                                                                                          |                                                                                                                                                        |                                                                                                                                                 |                                                                                                                                                 |
*/

/*
10kk records with indexes
QUERY PLAN                                                                                                                                                                                      |
------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
Limit  (cost=75783.13..75783.14 rows=3 width=87) (actual time=742.394..751.801 rows=3 loops=1)                                                                                                  |
  ->  Sort  (cost=75783.13..75804.33 rows=8481 width=87) (actual time=742.393..751.799 rows=3 loops=1)                                                                                          |
        Sort Key: (sum(t.price)) DESC                                                                                                                                                           |
        Sort Method: top-N heapsort  Memory: 25kB                                                                                                                                               |
        ->  Finalize GroupAggregate  (cost=74627.99..75673.51 rows=8481 width=87) (actual time=722.284..748.883 rows=16051 loops=1)                                                             |
              Group Key: m.id                                                                                                                                                                   |
              ->  Gather Merge  (cost=74627.99..75514.49 rows=7068 width=87) (actual time=722.275..738.702 rows=16084 loops=1)                                                                  |
                    Workers Planned: 2                                                                                                                                                          |
                    Workers Launched: 2                                                                                                                                                         |
                    ->  Partial GroupAggregate  (cost=73627.97..73698.65 rows=3534 width=87) (actual time=683.858..687.795 rows=5361 loops=3)                                                   |
                          Group Key: m.id                                                                                                                                                       |
                          ->  Sort  (cost=73627.97..73636.80 rows=3534 width=60) (actual time=683.849..684.383 rows=5366 loops=3)                                                               |
                                Sort Key: m.id                                                                                                                                                  |
                                Sort Method: quicksort  Memory: 698kB                                                                                                                           |
                                Worker 0:  Sort Method: quicksort  Memory: 636kB                                                                                                                |
                                Worker 1:  Sort Method: quicksort  Memory: 626kB                                                                                                                |
                                ->  Nested Loop  (cost=341.86..73419.69 rows=3534 width=60) (actual time=5.675..679.458 rows=5366 loops=3)                                                      |
                                      ->  Nested Loop  (cost=341.43..71629.98 rows=3534 width=9) (actual time=5.512..485.384 rows=5366 loops=3)                                                 |
                                            ->  Nested Loop  (cost=340.99..69910.88 rows=3534 width=9) (actual time=5.248..294.612 rows=5366 loops=3)                                           |
                                                  ->  Parallel Bitmap Heap Scan on orders o  (cost=340.56..35409.18 rows=3534 width=4) (actual time=4.987..135.949 rows=5351 loops=3)           |
                                                        Recheck Cond: ((date(paid_at) >= (CURRENT_DATE - '7 days'::interval)) AND (date(paid_at) < CURRENT_DATE))                               |
                                                        Filter: (status = 'paid'::order_status)                                                                                                 |
                                                        Rows Removed by Filter: 10706                                                                                                           |
                                                        Heap Blocks: exact=10078                                                                                                                |
                                                        ->  Bitmap Index Scan on "idx-orders-paid_at_date"  (cost=0.00..338.44 rows=25000 width=0) (actual time=9.261..9.261 rows=48171 loops=1)|
                                                              Index Cond: ((date(paid_at) >= (CURRENT_DATE - '7 days'::interval)) AND (date(paid_at) < CURRENT_DATE))                           |
                                                  ->  Index Scan using "idx-tickets-order_id" on tickets t  (cost=0.43..9.74 rows=2 width=13) (actual time=0.022..0.029 rows=1 loops=16052)     |
                                                        Index Cond: (order_id = o.id)                                                                                                           |
                                            ->  Index Scan using sessions_pkey on sessions s  (cost=0.43..0.49 rows=1 width=8) (actual time=0.035..0.035 rows=1 loops=16098)                    |
                                                  Index Cond: (id = t.session_id)                                                                                                               |
                                      ->  Index Scan using movies_pkey on movies m  (cost=0.43..0.51 rows=1 width=55) (actual time=0.035..0.035 rows=1 loops=16098)                             |
                                            Index Cond: (id = s.movie_id)                                                                                                                       |
Planning Time: 1.219 ms                                                                                                                                                                         |
Execution Time: 752.116 ms                                                                                                                                                                      |
*/