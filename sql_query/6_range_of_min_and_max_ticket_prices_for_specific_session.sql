-- Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс

EXPLAIN ANALYZE
SELECT MIN(price) AS min_price,
       MAX(price) AS max_price
FROM price_list
WHERE session_id = 10000;

/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000 строк
------------------------------------------------------------------------------------------------------------------------
Aggregate  (cost=189.01..189.02 rows=1 width=64) (actual time=0.367..0.368 rows=1 loops=1)
  ->  Seq Scan on price_list  (cost=0.00..189.00 rows=2 width=5) (actual time=0.127..0.363 rows=1 loops=1)
        Filter: (session_id = 10000)
        Rows Removed by Filter: 9999
Planning Time: 0.082 ms
Execution Time: 0.390 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк
------------------------------------------------------------------------------------------------------------------------
Aggregate  (cost=116893.66..116893.67 rows=1 width=64) (actual time=371.754..374.444 rows=1 loops=1)
  ->  Gather  (cost=1000.00..116893.65 rows=2 width=5) (actual time=7.255..374.384 rows=4 loops=1)
        Workers Planned: 2
        Workers Launched: 2
        ->  Parallel Seq Scan on price_list  (cost=0.00..115893.45 rows=1 width=5) (actual time=160.416..355.408 rows=1 loops=3)
              Filter: (session_id = 10000)
              Rows Removed by Filter: 3336665
Planning Time: 0.507 ms
JIT:
  Functions: 14
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 0.893 ms (Deform 0.293 ms), Inlining 0.000 ms, Optimization 0.828 ms, Emission 10.043 ms, Total 11.764 ms"
Execution Time: 374.963 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк после индексации
------------------------------------------------------------------------------------------------------------------------
Aggregate  (cost=12.48..12.49 rows=1 width=64) (actual time=0.057..0.058 rows=1 loops=1)
"  ->  Index Scan using ""idx-price_list-session_id"" on price_list  (cost=0.43..12.47 rows=2 width=5) (actual time=0.040..0.051 rows=4 loops=1)"
        Index Cond: (session_id = 10000)
Planning Time: 0.089 ms
Execution Time: 0.074 ms
------------------------------------------------------------------------------------------------------------------------
*/