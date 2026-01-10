-- Формирование афиши (фильмы, которые показывают сегодня)

EXPLAIN ANALYZE
SELECT films.*,
       sessions.created_at AS created_session
FROM films
    JOIN sessions
        ON films.id = sessions.film_id
WHERE DATE(sessions.created_at) = CURRENT_DATE
ORDER BY sessions.created_at;

/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000 строк
------------------------------------------------------------------------------------------------------------------------
Sort  (cost=559.54..559.66 rows=50 width=366) (actual time=1.018..1.019 rows=12 loops=1)
  Sort Key: sessions.created_at
  Sort Method: quicksort  Memory: 29kB
  ->  Nested Loop  (cost=0.29..558.13 rows=50 width=366) (actual time=0.095..0.994 rows=12 loops=1)
        ->  Seq Scan on sessions  (cost=0.00..239.00 rows=50 width=12) (actual time=0.086..0.930 rows=12 loops=1)
              Filter: (date(created_at) = CURRENT_DATE)
              Rows Removed by Filter: 9988
        ->  Index Scan using films_pkey on films  (cost=0.29..6.38 rows=1 width=358) (actual time=0.004..0.004 rows=1 loops=12)
              Index Cond: (id = sessions.film_id)
Planning Time: 0.442 ms
Execution Time: 1.055 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк
------------------------------------------------------------------------------------------------------------------------
Gather Merge  (cost=273439.02..278305.29 rows=41708 width=369) (actual time=785.709..793.599 rows=13643 loops=1)
  Workers Planned: 2
  Workers Launched: 2
  ->  Sort  (cost=272439.00..272491.13 rows=20854 width=369) (actual time=768.526..768.874 rows=4548 loops=3)
        Sort Key: sessions.created_at
        Sort Method: quicksort  Memory: 1940kB
        Worker 0:  Sort Method: quicksort  Memory: 1920kB
        Worker 1:  Sort Method: quicksort  Memory: 1940kB
        ->  Nested Loop  (cost=0.43..267376.43 rows=20854 width=369) (actual time=5.350..765.385 rows=4548 loops=3)
              ->  Parallel Seq Scan on sessions  (cost=0.00..136747.63 rows=20854 width=12) (actual time=4.946..340.951 rows=4548 loops=3)
                    Filter: (date(created_at) = CURRENT_DATE)
                    Rows Removed by Filter: 3332119
              ->  Index Scan using films_pkey on films  (cost=0.43..6.26 rows=1 width=361) (actual time=0.093..0.093 rows=1 loops=13643)
                    Index Cond: (id = sessions.film_id)
Planning Time: 5.398 ms
JIT:
  Functions: 24
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 2.153 ms (Deform 0.770 ms), Inlining 0.000 ms, Optimization 1.287 ms, Emission 12.067 ms, Total 15.507 ms"
Execution Time: 794.375 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк после индексации
------------------------------------------------------------------------------------------------------------------------
Gather Merge  (cost=200427.75..205294.03 rows=41708 width=369) (actual time=94.658..102.984 rows=13643 loops=1)
  Workers Planned: 2
  Workers Launched: 2
  ->  Sort  (cost=199427.73..199479.86 rows=20854 width=369) (actual time=75.239..75.567 rows=4548 loops=3)
        Sort Key: sessions.created_at
        Sort Method: quicksort  Memory: 2578kB
        Worker 0:  Sort Method: quicksort  Memory: 1462kB
        Worker 1:  Sort Method: quicksort  Memory: 1568kB
        ->  Nested Loop  (cost=560.76..194365.16 rows=20854 width=369) (actual time=6.924..73.150 rows=4548 loops=3)
              ->  Parallel Bitmap Heap Scan on sessions  (cost=560.33..63736.36 rows=20854 width=12) (actual time=2.930..23.143 rows=4548 loops=3)
                    Recheck Cond: (date(created_at) = CURRENT_DATE)
                    Heap Blocks: exact=5590
"                    ->  Bitmap Index Scan on ""idx-sessions-created_at_date""  (cost=0.00..547.81 rows=50050 width=0) (actual time=7.216..7.217 rows=13643 loops=1)"
                          Index Cond: (date(created_at) = CURRENT_DATE)
              ->  Index Scan using films_pkey on films  (cost=0.43..6.26 rows=1 width=361) (actual time=0.010..0.010 rows=1 loops=13643)
                    Index Cond: (id = sessions.film_id)
Planning Time: 0.245 ms
JIT:
  Functions: 27
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 1.714 ms (Deform 0.873 ms), Inlining 0.000 ms, Optimization 1.008 ms, Emission 15.237 ms, Total 17.958 ms"
Execution Time: 103.673 ms
------------------------------------------------------------------------------------------------------------------------
*/
