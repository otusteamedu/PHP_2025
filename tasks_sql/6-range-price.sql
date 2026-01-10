-- Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс

EXPLAIN ANALYZE
SELECT
    MIN(price) as min_price,
    MAX(price) as max_price
FROM
    tickets
WHERE
    session_id = 6951;

/*
10k записей без индексов

Aggregate  (cost=194.03..194.04 rows=1 width=64) (actual time=0.579..0.581 rows=1 loops=1)
  ->  Seq Scan on tickets  (cost=0.00..194.00 rows=6 width=5) (actual time=0.211..0.572 rows=6 loops=1)
        Filter: (session_id = 6951)
        Rows Removed by Filter: 9994
Planning Time: 0.143 ms
Execution Time: 0.614 ms

10kk записей без индексов

Aggregate  (cost=122196.72..122196.73 rows=1 width=64) (actual time=173.016..178.147 rows=1 loops=1)
  ->  Gather  (cost=1000.00..122196.71 rows=2 width=5) (actual time=172.968..178.097 rows=0 loops=1)
        Workers Planned: 2
        Workers Launched: 2
        ->  Parallel Seq Scan on tickets  (cost=0.00..121196.51 rows=1 width=5) (actual time=157.973..157.974 rows=0 loops=3)
              Filter: (session_id = 6951)
              Rows Removed by Filter: 3333333
Planning Time: 1.306 ms
JIT:
  Functions: 14
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 0.914 ms (Deform 0.349 ms), Inlining 0.000 ms, Optimization 0.771 ms, Emission 13.759 ms, Total 15.445 ms"
Execution Time: 178.589 ms


10kk записей с индексами

Aggregate  (cost=12.48..12.49 rows=1 width=64) (actual time=0.029..0.029 rows=1 loops=1)
"  ->  Index Scan using ""idx-tickets-session_id"" on tickets  (cost=0.43..12.47 rows=2 width=5) (actual time=0.025..0.025 rows=0 loops=1)"
        Index Cond: (session_id = 6951)
Planning Time: 0.200 ms
Execution Time: 0.058 ms


Execution Time: 0.614 ms
Execution Time: 178.589 ms
Execution Time: 0.058 ms

Выигрыш в 3000 раз

*/