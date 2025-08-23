EXPLAIN SELECT row, place,
       CASE
           WHEN t.id IS NULL THEN 'Свободно'
           ELSE 'Занято'
           END,
       sm.id
FROM schema_room
LEFT JOIN room ON schema_room.room_id = room.id
LEFT JOIN session_movie sm ON sm.room_id = room.id AND sm.id = 1
LEFT JOIN ticket t ON t.session_movie_id = sm.id AND schema_room.id = t.schema_id;

--   10.000 записей
--   Hash Right Join  (cost=376.98..595.65 rows=101250 width=44)
--   Hash Cond: (sm.room_id = room.id)
--   ->  Nested Loop Left Join  (cost=0.29..217.42 rows=9 width=12)
--         Join Filter: (t.session_movie_id = sm.id)
--         ->  Index Scan using session_movie_pkey on session_movie sm  (cost=0.29..8.30 rows=1 width=8)
--               Index Cond: (id = 1)
--         ->  Seq Scan on ticket t  (cost=0.00..209.00 rows=9 width=8)
--               Filter: (session_movie_id = 1)
--   ->  Hash  (cost=236.07..236.07 rows=11250 width=12)
--         ->  Hash Left Join  (cost=21.88..236.07 rows=11250 width=12)
--               Hash Cond: (schema_room.room_id = room.id)
--               ->  Seq Scan on schema_room  (cost=0.00..184.50 rows=11250 width=12)
--               ->  Hash  (cost=12.50..12.50 rows=750 width=4)
--                     ->  Seq Scan on room  (cost=0.00..12.50 rows=750 width=4)

-- 10.000.000
--   Hash Left Join  (cost=1154.14..18552.21 rows=3600000 width=44)
--   Hash Cond: (room.id = sm.room_id)
--   ->  Hash Left Join  (cost=1117.00..12514.17 rows=600000 width=12)
--         Hash Cond: (schema_room.room_id = room.id)
--         ->  Seq Scan on schema_room  (cost=0.00..9822.00 rows=600000 width=12)
--         ->  Hash  (cost=617.00..617.00 rows=40000 width=4)
--               ->  Seq Scan on room  (cost=0.00..617.00 rows=40000 width=4)
--   ->  Hash  (cost=37.06..37.06 rows=6 width=12)
--         ->  Nested Loop Left Join  (cost=0.87..37.06 rows=6 width=12)
--               Join Filter: (t.session_movie_id = sm.id)
--               ->  Index Scan using session_movie_pkey on session_movie sm  (cost=0.43..8.45 rows=1 width=8)
--                     Index Cond: (id = 1)
--               ->  Index Scan using idx_ticket_session_movie_id on ticket t  (cost=0.43..28.54 rows=6 width=8)
--                     Index Cond: (session_movie_id = 1)
-- Execution Time: 311.150 ms
-- Оптимизация:
-- 1) Переписан запрос. Сначала фильтруем данные, а потом по индексам выбираем нужные данные
--  EXPLAIN ANALYZE
--  SELECT sr.row, sr.place,
--        CASE WHEN t.id IS NULL THEN 'Свободно' ELSE 'Занято' END as status,
--        sm.id
--  FROM session_movie sm
--      LEFT JOIN room r ON sm.room_id = r.id
--      LEFT JOIN schema_room sr ON sr.room_id = r.id
--      LEFT JOIN ticket t ON t.session_movie_id = sm.id AND t.schema_id = sr.id
--  WHERE sm.id = 1;
-- 2) Добавлены индексы на внешние ключи

CREATE INDEX idx_ticket_session_movie_room_id ON session_movie (room_id);
CREATE INDEX idx_ticket_schema_room_room_id ON schema_room (room_id);

-- Hash Right Join  (cost=18.70..46.86 rows=15 width=44) (actual time=0.244..0.253 rows=15 loops=1)
--   Hash Cond: ((t.session_movie_id = sm.id) AND (t.schema_id = sr.id))
--   ->  Index Scan using idx_ticket_session_movie_id on ticket t  (cost=0.43..28.54 rows=6 width=12) (actual time=0.017..0.020 rows=3 loops=1)
--         Index Cond: (session_movie_id = 1)
--   ->  Hash  (cost=18.04..18.04 rows=15 width=16) (actual time=0.103..0.104 rows=15 loops=1)
--         Buckets: 1024  Batches: 1  Memory Usage: 9kB
--         ->  Nested Loop Left Join  (cost=1.15..18.04 rows=15 width=16) (actual time=0.087..0.091 rows=15 loops=1)
--               ->  Nested Loop Left Join  (cost=0.72..16.76 rows=1 width=8) (actual time=0.064..0.064 rows=1 loops=1)
--                     ->  Index Scan using session_movie_pkey on session_movie sm  (cost=0.43..8.45 rows=1 width=8) (actual time=0.031..0.031 rows=1 loops=1)
--                           Index Cond: (id = 1)
--                     ->  Index Only Scan using room_pkey on room r  (cost=0.29..8.31 rows=1 width=4) (actual time=0.029..0.029 rows=1 loops=1)
--                           Index Cond: (id = sm.room_id)
--                           Heap Fetches: 0
--               ->  Index Scan using idx_ticket_schema_room_room_id on schema_room sr  (cost=0.42..1.13 rows=15 width=16) (actual time=0.022..0.024 rows=15 loops=1)
--                     Index Cond: (room_id = r.id)
-- Planning Time: 0.717 ms
-- Execution Time: 0.478 ms
