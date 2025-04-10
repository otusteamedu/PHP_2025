/* Выбор фильмов на сегодня */
EXPLAIN ANALYZE
SELECT f.title FROM films F
    JOIN session S ON F.id = S.film_id
WHERE S.start_time >= CURRENT_DATE
  AND S.start_time < CURRENT_DATE + INTERVAL '1 day';

/*
10k записей без индексов

Nested Loop  (cost=0.29..540.13 rows=50 width=51) (actual time=0.106..1.075 rows=9 loops=1)
  ->  Seq Scan on session s  (cost=0.00..249.00 rows=50 width=4) (actual time=0.095..1.019 rows=9 loops=1)
        Filter: (date(start_time) = CURRENT_DATE)
        Rows Removed by Filter: 9991
  ->  Index Scan using films_pkey on films f  (cost=0.29..5.82 rows=1 width=55) (actual time=0.005..0.005 rows=1 loops=9)
        Index Cond: (id = s.film_id)
Planning Time: 0.486 ms
Execution Time: 1.113 ms

10kk записей без индексов
Gather  (cost=1000.43..213148.16 rows=14136 width=51) (actual time=4.902..511.296 rows=13646 loops=1)
  Workers Planned: 2
  Workers Launched: 2
  ->  Nested Loop  (cost=0.43..210734.56 rows=5890 width=51) (actual time=5.403..484.201 rows=4549 loops=3)
        ->  Parallel Seq Scan on session s  (cost=0.00..167286.00 rows=5890 width=4) (actual time=5.217..362.029 rows=4549 loops=3)
              Filter: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
              Rows Removed by Filter: 3328785
        ->  Index Scan using films_pkey on films f  (cost=0.43..7.38 rows=1 width=55) (actual time=0.026..0.026 rows=1 loops=13646)
              Index Cond: (id = s.film_id)
Planning Time: 1.168 ms
JIT:
  Functions: 21
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 1.959 ms (Deform 0.458 ms), Inlining 0.000 ms, Optimization 0.795 ms, Emission 14.261 ms, Total 17.015 ms"
Execution Time: 512.721 ms


10kk записей с индексами
Gather  (cost=1197.77..81576.69 rows=14136 width=51) (actual time=4.378..134.736 rows=13646 loops=1)
  Workers Planned: 2
  Workers Launched: 2
  ->  Nested Loop  (cost=197.77..79163.09 rows=5890 width=51) (actual time=1.517..107.669 rows=4549 loops=3)
        ->  Parallel Bitmap Heap Scan on session s  (cost=197.34..35714.53 rows=5890 width=4) (actual time=1.426..32.713 rows=4549 loops=3)
              Recheck Cond: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
              Heap Blocks: exact=5488
"              ->  Bitmap Index Scan on ""idx-sessions-start_time""  (cost=0.00..193.80 rows=14136 width=0) (actual time=2.539..2.539 rows=13646 loops=1)"
                    Index Cond: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
        ->  Index Scan using films_pkey on films f  (cost=0.43..7.38 rows=1 width=55) (actual time=0.016..0.016 rows=1 loops=13646)
              Index Cond: (id = s.film_id)
Planning Time: 1.796 ms
Execution Time: 135.317 ms


Execution Time: 1.113 ms
Execution Time: 512.721 ms
Execution Time: 135.317 ms

Выигрыш в 3,79 раз
*/