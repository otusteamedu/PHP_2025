/* Все покупки за неделю */
EXPLAIN ANALYZE
SELECT count(*) FROM tickets T
    JOIN orders ORD ON ORD.id = T.order_id
WHERE
      ORD.status='paid'
  AND ORD.created_at >= CURRENT_DATE - INTERVAL '7 days'
  AND ORD.created_at < CURRENT_DATE + INTERVAL '1 day'

/*
10k записей без индексов

Aggregate  (cost=562.62..562.63 rows=1 width=8) (actual time=2.420..2.423 rows=1 loops=1)
  ->  Hash Join  (cost=367.30..562.56 rows=24 width=0) (actual time=1.248..2.416 rows=52 loops=1)
        Hash Cond: (t.order_id = ord.id)
        ->  Seq Scan on tickets t  (cost=0.00..169.00 rows=10000 width=4) (actual time=0.008..0.471 rows=10000 loops=1)
        ->  Hash  (cost=367.00..367.00 rows=24 width=4) (actual time=1.232..1.233 rows=54 loops=1)
              Buckets: 1024  Batches: 1  Memory Usage: 10kB
              ->  Seq Scan on orders ord  (cost=0.00..367.00 rows=24 width=4) (actual time=0.027..1.223 rows=54 loops=1)
                    Filter: (((status)::text = 'paid'::text) AND (date(created_at) <= CURRENT_DATE) AND (date(created_at) >= (CURRENT_DATE - '7 days'::interval)))
                    Rows Removed by Filter: 9946
Planning Time: 0.326 ms
Execution Time: 2.457 ms

10kk записей без индексов
Finalize Aggregate  (cost=304006.48..304006.49 rows=1 width=8) (actual time=1184.804..1192.281 rows=1 loops=1)
  ->  Gather  (cost=304006.27..304006.48 rows=2 width=8) (actual time=1184.212..1192.246 rows=3 loops=1)
        Workers Planned: 2
        Workers Launched: 2
        ->  Partial Aggregate  (cost=303006.27..303006.28 rows=1 width=8) (actual time=1168.362..1168.366 rows=1 loops=3)
              ->  Parallel Hash Join  (cost=181226.97..302951.14 rows=22051 width=0) (actual time=440.728..1167.018 rows=16463 loops=3)
                    Hash Cond: (t.order_id = ord.id)
                    ->  Parallel Seq Scan on tickets t  (cost=0.00..110786.67 rows=4166667 width=4) (actual time=0.045..220.677 rows=3333333 loops=3)
                    ->  Parallel Hash  (cost=180951.33..180951.33 rows=22051 width=4) (actual time=440.059..440.060 rows=18286 loops=3)
                          Buckets: 65536  Batches: 1  Memory Usage: 2688kB
                          ->  Parallel Seq Scan on orders ord  (cost=0.00..180951.33 rows=22051 width=4) (actual time=7.358..433.730 rows=18286 loops=3)
                                Filter: (((status)::text = 'paid'::text) AND (created_at >= (CURRENT_DATE - '7 days'::interval)) AND (created_at < (CURRENT_DATE + '1 day'::interval)))
                                Rows Removed by Filter: 3315047
Planning Time: 0.224 ms
JIT:
  Functions: 41
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 1.918 ms (Deform 0.695 ms), Inlining 0.000 ms, Optimization 1.020 ms, Emission 20.996 ms, Total 23.933 ms"
Execution Time: 1193.018 ms



10kk записей с индексами

Finalize Aggregate  (cost=121813.73..121813.74 rows=1 width=8) (actual time=392.062..400.502 rows=1 loops=1)
  ->  Gather  (cost=121813.52..121813.73 rows=2 width=8) (actual time=391.102..400.488 rows=3 loops=1)
        Workers Planned: 2
        Workers Launched: 2
        ->  Partial Aggregate  (cost=120813.52..120813.53 rows=1 width=8) (actual time=340.826..340.828 rows=1 loops=3)
              ->  Nested Loop  (cost=1442.17..120758.39 rows=22051 width=0) (actual time=18.614..339.114 rows=16463 loops=3)
                    ->  Parallel Bitmap Heap Scan on orders ord  (cost=1441.74..71834.78 rows=22051 width=4) (actual time=18.545..161.069 rows=18286 loops=3)
                          Recheck Cond: ((created_at >= (CURRENT_DATE - '7 days'::interval)) AND (created_at < (CURRENT_DATE + '1 day'::interval)))
                          Filter: ((status)::text = 'paid'::text)
                          Rows Removed by Filter: 18101
                          Heap Blocks: exact=24149
"                          ->  Bitmap Index Scan on ""idx-orders-created_at""  (cost=0.00..1428.50 rows=105206 width=0) (actual time=30.482..30.482 rows=109160 loops=1)"
                                Index Cond: ((created_at >= (CURRENT_DATE - '7 days'::interval)) AND (created_at < (CURRENT_DATE + '1 day'::interval)))
"                    ->  Index Only Scan using ""idx-tickets-order_id"" on tickets t  (cost=0.43..2.20 rows=2 width=4) (actual time=0.009..0.009 rows=1 loops=54858)"
                          Index Cond: (order_id = ord.id)
                          Heap Fetches: 0
Planning Time: 3.640 ms
JIT:
  Functions: 35
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 14.073 ms (Deform 0.442 ms), Inlining 0.000 ms, Optimization 1.149 ms, Emission 18.729 ms, Total 33.952 ms"
Execution Time: 401.261 ms


Execution Time: 2.457 ms
Execution Time: 1193.018 ms
Execution Time: 401.261 ms

Выигрыш в 2,97 раз
*/