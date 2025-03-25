-- =======================================
-- Без индексов, только первичные ключи на 10_000
-- =======================================
-- 5) Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
-- =======================================
SELECT s.number,
       (CASE
            WHEN t.id IS NULL THEN 'Свободно'
            ELSE 'Занято'
           END) AS seast
FROM sessions AS ses
         JOIN seats s ON ses.hall_id = s.hall_id
         LEFT JOIN tickets t ON ses.id = t.session_id AND s.id = t.seat_id
WHERE ses.id = 4576
--     QUERY PLAN                                                                                                                                 |
-- -------------------------------------------------------------------------------------------------------------------------------------------+
-- Nested Loop Left Join  (cost=8.31..4908.64 rows=12 width=40) (actual time=5.273..18.511 rows=12 loops=1)                                   |
--   Join Filter: (s.id = t.seat_id)                                                                                                          |
--   Rows Removed by Join Filter: 144                                                                                                         |
--   ->  Hash Join  (cost=8.31..2523.45 rows=12 width=24) (actual time=1.369..14.593 rows=12 loops=1)                                         |
--         Hash Cond: (s.hall_id = ses.hall_id)                                                                                               |
--         ->  Seq Scan on seats s  (cost=0.00..2200.00 rows=120000 width=24) (actual time=0.013..8.683 rows=120000 loops=1)                  |
--     ->  Hash  (cost=8.30..8.30 rows=1 width=16) (actual time=0.015..0.015 rows=1 loops=1)                                              |
--     Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                 |
--     ->  Index Scan using sessions_pk on sessions ses  (cost=0.29..8.30 rows=1 width=16) (actual time=0.010..0.011 rows=1 loops=1)|
--     Index Cond: (id = 4576)                                                                                                |
--     ->  Materialize  (cost=0.00..2383.06 rows=12 width=24) (actual time=0.016..0.325 rows=12 loops=12)                                       |
--     ->  Seq Scan on tickets t  (cost=0.00..2383.00 rows=12 width=24) (actual time=0.185..3.892 rows=12 loops=1)                        |
--     Filter: (session_id = 4576)                                                                                                  |
--     Rows Removed by Filter: 119988                                                                                               |
--     Planning Time: 1.607 ms                                                                                                                    |
--     Execution Time: 18.544 ms                                                                                                                  |
-- =======================================
-- Без индексов, только первичные ключи на 10_000_000
-- =======================================
--     QUERY PLAN                                                                                                                                          |
-- ----------------------------------------------------------------------------------------------------------------------------------------------------+
-- Nested Loop Left Join  (cost=1008.47..1632314.12 rows=12 width=40) (actual time=64.160..14784.286 rows=12 loops=1)                                  |
--   Join Filter: (s.id = t.seat_id)                                                                                                                   |
--   Rows Removed by Join Filter: 144                                                                                                                  |
--   ->  Gather  (cost=1008.47..1632259.72 rows=12 width=24) (actual time=61.367..14781.433 rows=12 loops=1)                                           |
--         Workers Planned: 2                                                                                                                          |
--         Workers Launched: 2                                                                                                                         |
--         ->  Hash Join  (cost=8.46..1631258.52 rows=5 width=24) (actual time=1635.583..14756.839 rows=4 loops=3)                                     |
--               Hash Cond: (s.hall_id = ses.hall_id)                                                                                                  |
--               ->  Parallel Seq Scan on seats s  (cost=0.00..1500000.00 rows=50000000 width=24) (actual time=68.027..12749.088 rows=40000000 loops=3)|
--     ->  Hash  (cost=8.45..8.45 rows=1 width=16) (actual time=0.528..0.530 rows=1 loops=3)                                                 |
--     Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                    |
--     ->  Index Scan using sessions_pk on sessions ses  (cost=0.43..8.45 rows=1 width=16) (actual time=0.523..0.524 rows=1 loops=3)   |
--     Index Cond: (id = 4576)                                                                                                   |
--     ->  Materialize  (cost=0.00..52.27 rows=12 width=24) (actual time=0.038..0.232 rows=12 loops=12)                                                  |
--     ->  Index Scan using idx_session_id on tickets t  (cost=0.00..52.21 rows=12 width=24) (actual time=0.444..2.765 rows=12 loops=1)      |
--     Index Cond: (session_id = 4576)                                                                                                       |
--     Planning Time: 22.300 ms                                                                                                                            |
--     JIT:                                                                                                                                                |
--     Functions: 44                                                                                                                                     |
--     Options: Inlining true, Optimization true, Expressions true, Deforming true                                                                       |
--     Timing: Generation 3.878 ms (Deform 1.093 ms), Inlining 98.613 ms, Optimization 56.044 ms, Emission 47.961 ms, Total 206.496 ms                   |
--     Execution Time: 14786.338 ms                                                                                                                        |
-- =======================================
-- После индексов 10_000_000
-- =======================================
-- немного уменьшился cost
CREATE INDEX idx_id ON sessions USING hash (id);
-- так же используется idx_session_id созданный в 3_4_queries.sql
-- QUERY PLAN                                                                                                                                           |
-- -----------------------------------------------------------------------------------------------------------------------------------------------------+
-- Nested Loop Left Join  (cost=1008.03..1632313.69 rows=12 width=40) (actual time=66.965..12961.691 rows=12 loops=1)                                   |
--   Join Filter: (s.id = t.seat_id)                                                                                                                    |
--   Rows Removed by Join Filter: 144                                                                                                                   |
--   ->  Gather  (cost=1008.03..1632259.29 rows=12 width=24) (actual time=63.819..12958.457 rows=12 loops=1)                                            |
--         Workers Planned: 2                                                                                                                           |
--         Workers Launched: 2                                                                                                                          |
--         ->  Hash Join  (cost=8.03..1631258.09 rows=5 width=24) (actual time=1531.143..12926.714 rows=4 loops=3)                                      |
--               Hash Cond: (s.hall_id = ses.hall_id)                                                                                                   |
--               ->  Parallel Seq Scan on seats s  (cost=0.00..1500000.00 rows=50000000 width=24) (actual time=115.215..10874.446 rows=40000000 loops=3)|
--               ->  Hash  (cost=8.02..8.02 rows=1 width=16) (actual time=0.092..0.093 rows=1 loops=3)                                                  |
--                     Buckets: 1024  Batches: 1  Memory Usage: 9kB                                                                                     |
--                     ->  Index Scan using idx_id on sessions ses  (cost=0.00..8.02 rows=1 width=16) (actual time=0.088..0.089 rows=1 loops=3)         |
--                           Index Cond: (id = 4576)                                                                                                    |
--   ->  Materialize  (cost=0.00..52.27 rows=12 width=24) (actual time=0.064..0.262 rows=12 loops=12)                                                   |
--         ->  Index Scan using idx_session_id on tickets t  (cost=0.00..52.21 rows=12 width=24) (actual time=0.764..3.119 rows=12 loops=1)       |
--               Index Cond: (session_id = 4576)                                                                                                        |
-- Planning Time: 36.911 ms                                                                                                                             |
-- JIT:                                                                                                                                                 |
--   Functions: 44                                                                                                                                      |
--   Options: Inlining true, Optimization true, Expressions true, Deforming true                                                                        |
--   Timing: Generation 7.167 ms (Deform 2.156 ms), Inlining 88.719 ms, Optimization 53.675 ms, Emission 47.808 ms, Total 197.368 ms                    |
-- Execution Time: 12966.613 ms                                                                                                                         |
