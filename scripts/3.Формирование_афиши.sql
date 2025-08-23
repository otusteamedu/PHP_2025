EXPLAIN SELECT
    h.num as  "Номер зала",
    r.num as  "Номер комнаты",
    session_movie.time_start as  "Время начала",
    r.effect_type as  "Эффект",
    m.name as "Фильм",
    m.duration as "Продолжительность",
    m.description as "Описание"
    from session_movie
LEFT JOIN movie m ON session_movie.movie_id = m.id
LEFT JOIN room r ON session_movie.room_id = r.id
LEFT JOIN hall h ON r.hall_id = h.id
WHERE session_movie.time_start >= CURRENT_DATE
AND session_movie.time_start < CURRENT_DATE + INTERVAL '1 day';

-- 10.000 записей
--     Nested Loop  (cost=22.20..271.76 rows=50 width=101)
--   ->  Hash Join  (cost=22.04..264.48 rows=50 width=101)
--         Hash Cond: (session_movie.room_id = r.id)
--         ->  Nested Loop  (cost=0.16..242.48 rows=50 width=94)
--               ->  Seq Scan on session_movie  (cost=0.00..239.00 rows=50 width=16)
--                     Filter: (date(time_start) = CURRENT_DATE)
--               ->  Memoize  (cost=0.16..1.14 rows=1 width=86)
--                     Cache Key: session_movie.movie_id
--                     Cache Mode: logical
--                     ->  Index Scan using movie_pkey on movie m  (cost=0.15..1.13 rows=1 width=86)
--                           Index Cond: (id = session_movie.movie_id)
--         ->  Hash  (cost=12.50..12.50 rows=750 width=15)
--               ->  Seq Scan on room r  (cost=0.00..12.50 rows=750 width=15)
--   ->  Memoize  (cost=0.17..0.25 rows=1 width=8)
--         Cache Key: r.hall_id
--         Cache Mode: logical
--         ->  Index Scan using hall_pkey on hall h  (cost=0.15..0.24 rows=1 width=8)
--               Index Cond: (id = r.hall_id)

-- 10.000.000  записей
-- Gather  (cost=1006.10..160707.08 rows=14531 width=101)
--         Workers Planned: 2
--   ->  Hash Left Join  (cost=6.09..158253.98 rows=6055 width=101)
--         Hash Cond: (r.hall_id = h.id)
--         ->  Nested Loop Left Join  (cost=0.59..158232.25 rows=6055 width=101)
--               ->  Nested Loop Left Join  (cost=0.30..157685.67 rows=6055 width=94)
--                     ->  Parallel Seq Scan on session_movie  (cost=0.00..157445.00 rows=6055 width=16)
--                           Filter: ((time_start >= CURRENT_DATE) AND (time_start < (CURRENT_DATE + '1 day'::interval)))
--                     ->  Memoize  (cost=0.30..0.37 rows=1 width=86)
--                           Cache Key: session_movie.movie_id
--                           Cache Mode: logical
--                           ->  Index Scan using movie_pkey on movie m  (cost=0.29..0.36 rows=1 width=86)
--                                 Index Cond: (id = session_movie.movie_id)
--               ->  Memoize  (cost=0.30..0.41 rows=1 width=15)
--                     Cache Key: session_movie.room_id
--                     Cache Mode: logical
--                     ->  Index Scan using room_pkey on room r  (cost=0.29..0.40 rows=1 width=15)
--                           Index Cond: (id = session_movie.room_id)
--         ->  Hash  (cost=3.00..3.00 rows=200 width=8)
--               ->  Seq Scan on hall h  (cost=0.00..3.00 rows=200 width=8)
-- JIT:
--   Functions: 29
-- "  Options: Inlining false, Optimization false, Expressions true, Deforming true"
-- Execution Time: 387.023 ms

-- Оптимизация
-- 1) Создане индексов "idx_session_movie_movie_id", "idx_session_movie_room_id", "idx_room_hall_id"

CREATE INDEX idx_session_movie_movie_id ON session_movie (movie_id);
CREATE INDEX idx_session_movie_room_id ON session_movie (room_id);
CREATE INDEX idx_room_hall_id ON room (hall_id);
CREATE INDEX idx_session_movie_time_start ON session_movie (time_start);

-- Nested Loop Left Join  (cost=202.14..36601.70 rows=14531 width=101)
--   ->  Nested Loop Left Join  (cost=201.98..36206.81 rows=14531 width=101)
--         ->  Nested Loop Left Join  (cost=201.68..35448.29 rows=14531 width=94)
--               ->  Bitmap Heap Scan on session_movie  (cost=201.39..34995.71 rows=14531 width=16)
--                     Recheck Cond: ((time_start >= CURRENT_DATE) AND (time_start < (CURRENT_DATE + '1 day'::interval)))
--                     ->  Bitmap Index Scan on idx_session_movie_time_start  (cost=0.00..197.75 rows=14531 width=0)
--                           Index Cond: ((time_start >= CURRENT_DATE) AND (time_start < (CURRENT_DATE + '1 day'::interval)))
--               ->  Memoize  (cost=0.30..0.37 rows=1 width=86)
--                     Cache Key: session_movie.movie_id
--                     Cache Mode: logical
--                     ->  Index Scan using movie_pkey on movie m  (cost=0.29..0.36 rows=1 width=86)
--                           Index Cond: (id = session_movie.movie_id)
--         ->  Memoize  (cost=0.30..0.41 rows=1 width=15)
--               Cache Key: session_movie.room_id
--               Cache Mode: logical
--               ->  Index Scan using room_pkey on room r  (cost=0.29..0.40 rows=1 width=15)
--                     Index Cond: (id = session_movie.room_id)
--   ->  Memoize  (cost=0.15..0.17 rows=1 width=8)
--         Cache Key: r.hall_id
--         Cache Mode: logical
--         ->  Index Scan using hall_pkey on hall h  (cost=0.14..0.16 rows=1 width=8)
--               Index Cond: (id = r.hall_id)
-- Execution Time: 12.394 ms

-- Время выполнения уменьшилось ~ в 4 раза
-- Стоимость уменьшилась ~ в 5 раза