-- Выбор всех фильмов на сегодня

EXPLAIN ANALYZE
SELECT
    m.name
FROM
    movies m
        JOIN sessions s ON m.id = s.movie_id
WHERE
    DATE(s.started_at) = CURRENT_DATE;

/*
10k records
QUERY PLAN                                                                                                                  |
----------------------------------------------------------------------------------------------------------------------------+
Nested Loop  (cost=0.29..534.13 rows=50 width=51) (actual time=0.100..1.319 rows=15 loops=1)                                |
  ->  Seq Scan on sessions s  (cost=0.00..239.00 rows=50 width=4) (actual time=0.093..1.276 rows=15 loops=1)                |
        Filter: (date(started_at) = CURRENT_DATE)                                                                           |
        Rows Removed by Filter: 9985                                                                                        |
  ->  Index Scan using movies_pkey on movies m  (cost=0.29..5.90 rows=1 width=55) (actual time=0.002..0.002 rows=1 loops=15)|
        Index Cond: (id = s.movie_id)                                                                                       |
Planning Time: 0.184 ms                                                                                                     |
Execution Time: 1.333 ms                                                                                                    |                                                                                                                      |                                                                                                                                        |                                                                                                   |                                                                                                            |
*/


/*
10kk records without indexes
QUERY PLAN                                                                                                                          |
------------------------------------------------------------------------------------------------------------------------------------+
Gather  (cost=1000.43..132445.77 rows=25000 width=51) (actual time=1.104..450.693 rows=6957 loops=1)                                |
  Workers Planned: 2                                                                                                                |
  Workers Launched: 2                                                                                                               |
  ->  Nested Loop  (cost=0.43..128945.77 rows=10417 width=51) (actual time=0.405..409.566 rows=2319 loops=3)                        |
        ->  Parallel Seq Scan on sessions s  (cost=0.00..68306.33 rows=10417 width=4) (actual time=0.352..376.752 rows=2319 loops=3)|
              Filter: (date(started_at) = CURRENT_DATE)                                                                             |
              Rows Removed by Filter: 1664348                                                                                       |
        ->  Index Scan using movies_pkey on movies m  (cost=0.43..5.82 rows=1 width=55) (actual time=0.013..0.013 rows=1 loops=6957)|
              Index Cond: (id = s.movie_id)                                                                                         |
Planning Time: 0.396 ms                                                                                                             |
Execution Time: 451.085 ms                                                                                                          |                                                                                                         |                                                                                                                        |
*/

/*
10kk records with indexes
QUERY PLAN                                                                                                                                                |
----------------------------------------------------------------------------------------------------------------------------------------------------------+
Gather  (cost=1282.62..95978.91 rows=25000 width=51) (actual time=2.963..163.165 rows=6957 loops=1)                                                       |
  Workers Planned: 2                                                                                                                                      |
  Workers Launched: 2                                                                                                                                     |
  ->  Nested Loop  (cost=282.62..92478.91 rows=10417 width=51) (actual time=0.984..117.689 rows=2319 loops=3)                                             |
        ->  Parallel Bitmap Heap Scan on sessions s  (cost=282.19..31839.47 rows=10417 width=4) (actual time=0.797..35.230 rows=2319 loops=3)             |
              Recheck Cond: (date(started_at) = CURRENT_DATE)                                                                                             |
              Heap Blocks: exact=3185                                                                                                                     |
              ->  Bitmap Index Scan on "idx-sessions-started_at_date"  (cost=0.00..275.94 rows=25000 width=0) (actual time=1.384..1.384 rows=6957 loops=1)|
                    Index Cond: (date(started_at) = CURRENT_DATE)                                                                                         |
        ->  Index Scan using movies_pkey on movies m  (cost=0.43..5.82 rows=1 width=55) (actual time=0.035..0.035 rows=1 loops=6957)                      |
              Index Cond: (id = s.movie_id)                                                                                                               |
Planning Time: 0.596 ms                                                                                                                                   |
Execution Time: 163.676 ms                                                                                                                                |                                                                                                                                       |                                                                                                                                                       |                                                                                                       |
*/