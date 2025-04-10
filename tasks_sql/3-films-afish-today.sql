/* Афиша фильмов на сегодня */
EXPLAIN ANALYZE
SELECT F.title, S.start_time
FROM
    films F
        JOIN session S ON F.id = S.film_id
WHERE
  S.start_time >= CURRENT_DATE
  AND S.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY
    S.start_time;

/*
10k записей без индексов

Sort  (cost=541.54..541.66 rows=50 width=59) (actual time=0.945..0.947 rows=9 loops=1)
  Sort Key: s.start_time
  Sort Method: quicksort  Memory: 25kB
  ->  Nested Loop  (cost=0.29..540.13 rows=50 width=59) (actual time=0.090..0.932 rows=9 loops=1)
        ->  Seq Scan on session s  (cost=0.00..249.00 rows=50 width=12) (actual time=0.084..0.903 rows=9 loops=1)
              Filter: (date(start_time) = CURRENT_DATE)
              Rows Removed by Filter: 9991
        ->  Index Scan using films_pkey on films f  (cost=0.29..5.82 rows=1 width=55) (actual time=0.003..0.003 rows=1 loops=9)
              Index Cond: (id = s.film_id)
Planning Time: 0.202 ms
Execution Time: 0.967 ms

10kk записей без индексов
Gather Merge  (cost=269049.75..273911.59 rows=41670 width=59) (actual time=483.777..494.430 rows=13646 loops=1)
  Workers Planned: 2
  Workers Launched: 2
  ->  Sort  (cost=268049.73..268101.81 rows=20835 width=59) (actual time=467.314..467.803 rows=4549 loops=3)
        Sort Key: s.start_time
        Sort Method: quicksort  Memory: 562kB
        Worker 0:  Sort Method: quicksort  Memory: 540kB
        Worker 1:  Sort Method: quicksort  Memory: 541kB
        ->  Nested Loop  (cost=0.43..266555.16 rows=20835 width=59) (actual time=6.287..465.421 rows=4549 loops=3)
              ->  Parallel Seq Scan on session s  (cost=0.00..146456.77 rows=20835 width=12) (actual time=6.140..362.685 rows=4549 loops=3)
                    Filter: (date(start_time) = CURRENT_DATE)
                    Rows Removed by Filter: 3328785
              ->  Index Scan using films_pkey on films f  (cost=0.43..5.76 rows=1 width=55) (actual time=0.022..0.022 rows=1 loops=13646)
                    Index Cond: (id = s.film_id)
Planning Time: 0.275 ms
JIT:
  Functions: 24
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 1.319 ms (Deform 0.478 ms), Inlining 0.000 ms, Optimization 1.089 ms, Emission 17.117 ms, Total 19.525 ms"
Execution Time: 495.362 ms


10kk записей с индексами
Gather Merge  (cost=80531.95..81906.38 rows=11780 width=59) (actual time=121.961..132.661 rows=13646 loops=1)
  Workers Planned: 2
  Workers Launched: 2
  ->  Sort  (cost=79531.92..79546.65 rows=5890 width=59) (actual time=106.337..106.832 rows=4549 loops=3)
        Sort Key: s.start_time
        Sort Method: quicksort  Memory: 672kB
        Worker 0:  Sort Method: quicksort  Memory: 381kB
        Worker 1:  Sort Method: quicksort  Memory: 398kB
        ->  Nested Loop  (cost=197.77..79163.09 rows=5890 width=59) (actual time=1.379..104.815 rows=4549 loops=3)
              ->  Parallel Bitmap Heap Scan on session s  (cost=197.34..35714.53 rows=5890 width=12) (actual time=1.327..34.387 rows=4549 loops=3)
                    Recheck Cond: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
                    Heap Blocks: exact=5635
"                    ->  Bitmap Index Scan on ""idx-sessions-start_time""  (cost=0.00..193.80 rows=14136 width=0) (actual time=2.344..2.344 rows=13646 loops=1)"
                          Index Cond: ((start_time >= CURRENT_DATE) AND (start_time < (CURRENT_DATE + '1 day'::interval)))
              ->  Index Scan using films_pkey on films f  (cost=0.43..7.38 rows=1 width=55) (actual time=0.015..0.015 rows=1 loops=13646)
                    Index Cond: (id = s.film_id)
Planning Time: 0.335 ms
Execution Time: 133.076 ms


Execution Time: 0.967 ms
Execution Time: 495.362 ms
Execution Time: 133.076 ms

Выигрыш в 3,72 раза

*/