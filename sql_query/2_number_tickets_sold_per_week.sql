-- Подсчёт проданных билетов за неделю

EXPLAIN ANALYZE
SELECT COUNT(tickets.id) AS number_tickets
FROM tickets
    JOIN orders
        ON orders.id = tickets.order_id
WHERE orders.status = 'paid'
    AND DATE (orders.paid_at) >= CURRENT_DATE - INTERVAL '1 week'
    AND DATE (orders.paid_at) < CURRENT_DATE;

/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000 строк
------------------------------------------------------------------------------------------------------------------------
Aggregate  (cost=574.52..574.53 rows=1 width=8) (actual time=1.500..1.501 rows=1 loops=1)
  ->  Hash Join  (cost=384.21..574.47 rows=17 width=4) (actual time=0.672..1.496 rows=37 loops=1)
        Hash Cond: (tickets.order_id = orders.id)
        ->  Seq Scan on tickets  (cost=0.00..164.00 rows=10000 width=8) (actual time=0.005..0.390 rows=10000 loops=1)
        ->  Hash  (cost=384.00..384.00 rows=17 width=4) (actual time=0.644..0.644 rows=31 loops=1)
              Buckets: 1024  Batches: 1  Memory Usage: 10kB
              ->  Seq Scan on orders  (cost=0.00..384.00 rows=17 width=4) (actual time=0.050..0.639 rows=31 loops=1)
                    Filter: ((status = 'paid'::enum_order_status) AND (date(paid_at) < CURRENT_DATE) AND (date(paid_at) >= (CURRENT_DATE - '7 days'::interval)))
                    Rows Removed by Filter: 9969
Planning Time: 0.218 ms
Execution Time: 1.525 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк
------------------------------------------------------------------------------------------------------------------------
Finalize Aggregate  (cost=326062.49..326062.50 rows=1 width=8) (actual time=1279.359..1282.391 rows=1 loops=1)
  ->  Gather  (cost=326062.28..326062.49 rows=2 width=8) (actual time=1279.168..1282.380 rows=3 loops=1)
        Workers Planned: 2
        Workers Launched: 2
        ->  Partial Aggregate  (cost=325062.28..325062.29 rows=1 width=8) (actual time=1263.406..1263.408 rows=1 loops=3)
              ->  Parallel Hash Join  (cost=208630.04..325044.77 rows=7003 width=4) (actual time=577.247..1262.778 rows=10748 loops=3)
                    Hash Cond: (tickets.order_id = orders.id)
                    ->  Parallel Seq Scan on tickets  (cost=0.00..105466.30 rows=4170830 width=8) (actual time=0.550..409.184 rows=3336667 loops=3)
                    ->  Parallel Hash  (cost=208542.50..208542.50 rows=7003 width=4) (actual time=576.247..576.248 rows=10756 loops=3)
                          Buckets: 32768  Batches: 1  Memory Usage: 1568kB
                          ->  Parallel Seq Scan on orders  (cost=0.00..208542.50 rows=7003 width=4) (actual time=7.426..571.424 rows=10756 loops=3)
                                Filter: ((status = 'paid'::enum_order_status) AND (date(paid_at) < CURRENT_DATE) AND (date(paid_at) >= (CURRENT_DATE - '7 days'::interval)))
                                Rows Removed by Filter: 3325911
Planning Time: 2.445 ms
JIT:
  Functions: 44
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 1.423 ms (Deform 0.415 ms), Inlining 0.000 ms, Optimization 1.296 ms, Emission 19.722 ms, Total 22.441 ms"
Execution Time: 1282.888 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк после индексации
------------------------------------------------------------------------------------------------------------------------
Finalize Aggregate  (cost=146386.00..146386.01 rows=1 width=8) (actual time=436.579..448.281 rows=1 loops=1)
  ->  Gather  (cost=146385.78..146385.99 rows=2 width=8) (actual time=436.115..448.266 rows=3 loops=1)
        Workers Planned: 2
        Workers Launched: 2
        ->  Partial Aggregate  (cost=145385.78..145385.79 rows=1 width=8) (actual time=410.413..410.415 rows=1 loops=3)
              ->  Nested Loop  (cost=677.58..145368.28 rows=7003 width=4) (actual time=20.035..409.432 rows=10748 loops=3)
                    ->  Parallel Bitmap Heap Scan on orders  (cost=677.14..76837.07 rows=7003 width=4) (actual time=19.968..310.341 rows=10756 loops=3)
                          Recheck Cond: ((date(paid_at) >= (CURRENT_DATE - '7 days'::interval)) AND (date(paid_at) < CURRENT_DATE))
                          Filter: (status = 'paid'::enum_order_status)
                          Rows Removed by Filter: 21506
                          Heap Blocks: exact=20391
"                          ->  Bitmap Index Scan on ""idx-orders-paid_at_date""  (cost=0.00..672.94 rows=50050 width=0) (actual time=31.233..31.234 rows=96787 loops=1)"
                                Index Cond: ((date(paid_at) >= (CURRENT_DATE - '7 days'::interval)) AND (date(paid_at) < CURRENT_DATE))
"                    ->  Index Scan using ""idx-tickets-order_id"" on tickets  (cost=0.43..9.77 rows=2 width=8) (actual time=0.007..0.009 rows=1 loops=32268)"
                          Index Cond: (order_id = orders.id)
Planning Time: 0.246 ms
JIT:
  Functions: 41
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 1.595 ms (Deform 0.419 ms), Inlining 0.000 ms, Optimization 1.182 ms, Emission 16.572 ms, Total 19.350 ms"
Execution Time: 448.796 ms
------------------------------------------------------------------------------------------------------------------------
*/