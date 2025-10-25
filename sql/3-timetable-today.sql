-- Формирование афиши (фильмы, которые показывают сегодня)

EXPLAIN ANALYZE
SELECT
    m.*,
    s.started_at
FROM
    movies m
        JOIN sessions s ON m.id = s.movie_id
WHERE
    DATE(s.started_at) = CURRENT_DATE
ORDER BY
    s.started_at;

/*
10k records
QUERY PLAN                                                                                                                        |
----------------------------------------------------------------------------------------------------------------------------------+
Sort  (cost=535.54..535.66 rows=50 width=67) (actual time=1.512..1.513 rows=15 loops=1)                                           |
  Sort Key: s.started_at                                                                                                          |
  Sort Method: quicksort  Memory: 26kB                                                                                            |
  ->  Nested Loop  (cost=0.29..534.13 rows=50 width=67) (actual time=0.101..1.499 rows=15 loops=1)                                |
        ->  Seq Scan on sessions s  (cost=0.00..239.00 rows=50 width=12) (actual time=0.093..1.442 rows=15 loops=1)               |
              Filter: (date(started_at) = CURRENT_DATE)                                                                           |
              Rows Removed by Filter: 9985                                                                                        |
        ->  Index Scan using movies_pkey on movies m  (cost=0.29..5.90 rows=1 width=59) (actual time=0.003..0.003 rows=1 loops=15)|
              Index Cond: (id = s.movie_id)                                                                                       |
Planning Time: 0.205 ms                                                                                                           |
Execution Time: 1.532 ms                                                                                                          |                                                                                                       |                                                                                                         |                                                                                                       |
*/

/*
10kk records without indexes
QUERY PLAN                                                                                                                                 |
-------------------------------------------------------------------------------------------------------------------------------------------+
Gather Merge  (cost=130640.96..133071.76 rows=20834 width=67) (actual time=420.610..432.143 rows=6957 loops=1)                             |
  Workers Planned: 2                                                                                                                       |
  Workers Launched: 2                                                                                                                      |
  ->  Sort  (cost=129640.93..129666.98 rows=10417 width=67) (actual time=383.990..384.162 rows=2319 loops=3)                               |
        Sort Key: s.started_at                                                                                                             |
        Sort Method: quicksort  Memory: 341kB                                                                                              |
        Worker 0:  Sort Method: quicksort  Memory: 300kB                                                                                   |
        Worker 1:  Sort Method: quicksort  Memory: 301kB                                                                                   |
        ->  Nested Loop  (cost=0.43..128945.77 rows=10417 width=67) (actual time=0.438..382.533 rows=2319 loops=3)                         |
              ->  Parallel Seq Scan on sessions s  (cost=0.00..68306.33 rows=10417 width=12) (actual time=0.396..356.021 rows=2319 loops=3)|
                    Filter: (date(started_at) = CURRENT_DATE)                                                                              |
                    Rows Removed by Filter: 1664348                                                                                        |
              ->  Index Scan using movies_pkey on movies m  (cost=0.43..5.82 rows=1 width=59) (actual time=0.011..0.011 rows=1 loops=6957) |
                    Index Cond: (id = s.movie_id)                                                                                          |
Planning Time: 0.274 ms                                                                                                                    |
Execution Time: 432.490 ms                                                                                                                 |                                                                                                                |                                                                                                               |                                                                                                               |
*/

/*
10kk records with indexes
QUERY PLAN                                                                                                                                                      |
----------------------------------------------------------------------------------------------------------------------------------------------------------------+
Gather Merge  (cost=94174.09..96604.89 rows=20834 width=67) (actual time=152.323..162.316 rows=6957 loops=1)                                                    |
  Workers Planned: 2                                                                                                                                            |
  Workers Launched: 2                                                                                                                                           |
  ->  Sort  (cost=93174.07..93200.11 rows=10417 width=67) (actual time=117.305..117.458 rows=2319 loops=3)                                                      |
        Sort Key: s.started_at                                                                                                                                  |
        Sort Method: quicksort  Memory: 425kB                                                                                                                   |
        Worker 0:  Sort Method: quicksort  Memory: 211kB                                                                                                        |
        Worker 1:  Sort Method: quicksort  Memory: 210kB                                                                                                        |
        ->  Nested Loop  (cost=282.62..92478.91 rows=10417 width=67) (actual time=0.885..115.468 rows=2319 loops=3)                                             |
              ->  Parallel Bitmap Heap Scan on sessions s  (cost=282.19..31839.47 rows=10417 width=12) (actual time=0.797..34.484 rows=2319 loops=3)            |
                    Recheck Cond: (date(started_at) = CURRENT_DATE)                                                                                             |
                    Heap Blocks: exact=3161                                                                                                                     |
                    ->  Bitmap Index Scan on "idx-sessions-started_at_date"  (cost=0.00..275.94 rows=25000 width=0) (actual time=1.243..1.243 rows=6957 loops=1)|
                          Index Cond: (date(started_at) = CURRENT_DATE)                                                                                         |
              ->  Index Scan using movies_pkey on movies m  (cost=0.43..5.82 rows=1 width=59) (actual time=0.034..0.034 rows=1 loops=6957)                      |
                    Index Cond: (id = s.movie_id)                                                                                                               |
Planning Time: 0.545 ms                                                                                                                                         |
Execution Time: 162.697 ms                                                                                                                                      |                                                                                                                                    |
*/