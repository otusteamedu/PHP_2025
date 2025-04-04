-- Выбор всех фильмов на сегодня:

EXPLAIN ANALYZE
SELECT title
FROM films
    JOIN sessions
        ON films.id = sessions.film_id
WHERE DATE (sessions.created_at) = CURRENT_DATE;

/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000 строк
------------------------------------------------------------------------------------------------------------------------
Nested Loop  (cost=0.29..558.13 rows=50 width=9) (actual time=0.065..0.735 rows=12 loops=1)
  ->  Seq Scan on sessions  (cost=0.00..239.00 rows=50 width=4) (actual time=0.058..0.697 rows=12 loops=1)
        Filter: (date(created_at) = CURRENT_DATE)
        Rows Removed by Filter: 9988
  ->  Index Scan using films_pkey on films  (cost=0.29..6.38 rows=1 width=13) (actual time=0.003..0.003 rows=1 loops=12)
        Index Cond: (id = sessions.film_id)
Planning Time: 0.323 ms
Execution Time: 0.762 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк
------------------------------------------------------------------------------------------------------------------------
Gather  (cost=1000.43..273381.43 rows=50050 width=12) (actual time=28.759..1477.404 rows=13643 loops=1)
  Workers Planned: 2
  Workers Launched: 2
  ->  Nested Loop  (cost=0.43..267376.43 rows=20854 width=12) (actual time=17.778..1458.922 rows=4548 loops=3)
        ->  Parallel Seq Scan on sessions  (cost=0.00..136747.63 rows=20854 width=4) (actual time=16.796..480.331 rows=4548 loops=3)
              Filter: (date(created_at) = CURRENT_DATE)
              Rows Removed by Filter: 3332119
        ->  Index Scan using films_pkey on films  (cost=0.43..6.26 rows=1 width=16) (actual time=0.215..0.215 rows=1 loops=13643)
              Index Cond: (id = sessions.film_id)
Planning Time: 5.729 ms
JIT:
  Functions: 21
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 1.065 ms (Deform 0.252 ms), Inlining 0.000 ms, Optimization 2.758 ms, Emission 43.497 ms, Total 47.320 ms"
Execution Time: 1597.971 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк после индексации
------------------------------------------------------------------------------------------------------------------------
Gather  (cost=1560.76..200370.16 rows=50050 width=12) (actual time=10.944..100.311 rows=13643 loops=1)
  Workers Planned: 2
  Workers Launched: 2
  ->  Nested Loop  (cost=560.76..194365.16 rows=20854 width=12) (actual time=5.977..73.557 rows=4548 loops=3)
        ->  Parallel Bitmap Heap Scan on sessions  (cost=560.33..63736.36 rows=20854 width=4) (actual time=3.511..24.164 rows=4548 loops=3)
              Recheck Cond: (date(created_at) = CURRENT_DATE)
              Heap Blocks: exact=5427
"              ->  Bitmap Index Scan on ""idx-sessions-created_at_date""  (cost=0.00..547.81 rows=50050 width=0) (actual time=8.968..8.968 rows=13643 loops=1)"
                    Index Cond: (date(created_at) = CURRENT_DATE)
        ->  Index Scan using films_pkey on films  (cost=0.43..6.26 rows=1 width=16) (actual time=0.010..0.010 rows=1 loops=13643)
              Index Cond: (id = sessions.film_id)
Planning Time: 0.239 ms
JIT:
  Functions: 24
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 1.293 ms (Deform 0.324 ms), Inlining 0.000 ms, Optimization 0.854 ms, Emission 11.558 ms, Total 13.705 ms"
Execution Time: 101.063 ms
------------------------------------------------------------------------------------------------------------------------
*/

