-- Подсчёт проданных билетов за неделю

EXPLAIN ANALYZE
SELECT
    COUNT(t.id) as tickets_num
FROM
    tickets t
        JOIN orders o ON o.id = t.order_id
WHERE
    o.status = 'paid'
    AND DATE(o.paid_at) >= CURRENT_DATE - INTERVAL '1 week' AND DATE(o.paid_at) < CURRENT_DATE;

/*
10k records
QUERY PLAN                                                                                                                                                 |
-----------------------------------------------------------------------------------------------------------------------------------------------------------+
Aggregate  (cost=564.52..564.53 rows=1 width=8) (actual time=2.577..2.578 rows=1 loops=1)                                                                  |
  ->  Hash Join  (cost=374.21..564.47 rows=17 width=4) (actual time=1.426..2.573 rows=18 loops=1)                                                          |
        Hash Cond: (t.order_id = o.id)                                                                                                                     |
        ->  Seq Scan on tickets t  (cost=0.00..164.00 rows=10000 width=8) (actual time=0.014..0.542 rows=10000 loops=1)                                    |
        ->  Hash  (cost=374.00..374.00 rows=17 width=4) (actual time=1.368..1.368 rows=23 loops=1)                                                         |
              Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                                 |
              ->  Seq Scan on orders o  (cost=0.00..374.00 rows=17 width=4) (actual time=0.037..1.364 rows=23 loops=1)                                     |
                    Filter: ((status = 'paid'::order_status) AND (date(paid_at) < CURRENT_DATE) AND (date(paid_at) >= (CURRENT_DATE - '7 days'::interval)))|
                    Rows Removed by Filter: 9977                                                                                                           |
Planning Time: 0.253 ms                                                                                                                                    |
Execution Time: 2.597 ms                                                                                                                                   |                                                                                                                                                 |                                                                                                              |                                                                                                                 |
*/

/*
10kk records without indexes
QUERY PLAN                                                                                                                                                             |
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------+
Finalize Aggregate  (cost=158468.31..158468.32 rows=1 width=8) (actual time=860.492..865.988 rows=1 loops=1)                                                           |
  ->  Gather  (cost=158468.09..158468.30 rows=2 width=8) (actual time=860.294..865.984 rows=3 loops=1)                                                                 |
        Workers Planned: 2                                                                                                                                             |
        Workers Launched: 2                                                                                                                                            |
        ->  Partial Aggregate  (cost=157468.09..157468.10 rows=1 width=8) (actual time=819.059..819.060 rows=1 loops=3)                                                |
              ->  Parallel Hash Join  (cost=99309.18..157459.26 rows=3534 width=4) (actual time=382.290..818.298 rows=5366 loops=3)                                    |
                    Hash Cond: (t.order_id = o.id)                                                                                                                     |
                    ->  Parallel Seq Scan on tickets t  (cost=0.00..52681.33 rows=2083333 width=8) (actual time=0.125..217.704 rows=1666667 loops=3)                   |
                    ->  Parallel Hash  (cost=99265.00..99265.00 rows=3534 width=4) (actual time=381.666..381.666 rows=5351 loops=3)                                    |
                          Buckets: 16384  Batches: 1  Memory Usage: 832kB                                                                                              |
                          ->  Parallel Seq Scan on orders o  (cost=0.00..99265.00 rows=3534 width=4) (actual time=0.288..379.202 rows=5351 loops=3)                    |
                                Filter: ((status = 'paid'::order_status) AND (date(paid_at) < CURRENT_DATE) AND (date(paid_at) >= (CURRENT_DATE - '7 days'::interval)))|
                                Rows Removed by Filter: 1661316                                                                                                        |
Planning Time: 0.190 ms                                                                                                                                                |
Execution Time: 866.023 ms                                                                                                                                             |                                                                                                                            |                                                                                                                          |
*/

/*
10kk records with indexes
QUERY PLAN                                                                                                                                                        |
------------------------------------------------------------------------------------------------------------------------------------------------------------------+
Finalize Aggregate  (cost=70919.93..70919.94 rows=1 width=8) (actual time=360.845..370.062 rows=1 loops=1)                                                        |
  ->  Gather  (cost=70919.71..70919.92 rows=2 width=8) (actual time=360.481..370.038 rows=3 loops=1)                                                              |
        Workers Planned: 2                                                                                                                                        |
        Workers Launched: 2                                                                                                                                       |
        ->  Partial Aggregate  (cost=69919.71..69919.72 rows=1 width=8) (actual time=318.690..318.691 rows=1 loops=3)                                             |
              ->  Nested Loop  (cost=340.99..69910.88 rows=3534 width=4) (actual time=5.048..317.434 rows=5366 loops=3)                                           |
                    ->  Parallel Bitmap Heap Scan on orders o  (cost=340.56..35409.18 rows=3534 width=4) (actual time=4.875..145.808 rows=5351 loops=3)           |
                          Recheck Cond: ((date(paid_at) >= (CURRENT_DATE - '7 days'::interval)) AND (date(paid_at) < CURRENT_DATE))                               |
                          Filter: (status = 'paid'::order_status)                                                                                                 |
                          Rows Removed by Filter: 10706                                                                                                           |
                          Heap Blocks: exact=10981                                                                                                                |
                          ->  Bitmap Index Scan on "idx-orders-paid_at_date"  (cost=0.00..338.44 rows=25000 width=0) (actual time=9.233..9.233 rows=48171 loops=1)|
                                Index Cond: ((date(paid_at) >= (CURRENT_DATE - '7 days'::interval)) AND (date(paid_at) < CURRENT_DATE))                           |
                    ->  Index Scan using "idx-tickets-order_id" on tickets t  (cost=0.43..9.74 rows=2 width=8) (actual time=0.024..0.031 rows=1 loops=16052)      |
                          Index Cond: (order_id = o.id)                                                                                                           |
Planning Time: 0.490 ms                                                                                                                                           |
Execution Time: 370.162 ms                                                                                                                                        |                                                                                                                                  |                                                                                                                                      |
*/