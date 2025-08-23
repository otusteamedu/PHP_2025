explain SELECT m.name FROM session_movie
   INNER JOIN movie m ON m.id = session_movie.movie_id
WHERE time_start::date = CURRENT_DATE;

-- 10.000 записей
-- Nested Loop  (cost=0.16..242.48 rows=50 width=33)
--   ->  Seq Scan on session_movie  (cost=0.00..239.00 rows=50 width=4)
--         Filter: ((time_start)::date = CURRENT_DATE)
--   ->  Memoize  (cost=0.16..1.14 rows=1 width=37)
--         Cache Key: session_movie.movie_id
--         Cache Mode: logical
--         ->  Index Scan using movie_pkey on movie m  (cost=0.15..1.13 rows=1 width=37)
--               Index Cond: (id = session_movie.movie_id)

-- 10.000.000 записей
-- Nested Loop  (cost=1000.29..128200.36 rows=40068 width=33)
--   ->  Gather  (cost=1000.00..127134.03 rows=40068 width=4)
--         Workers Planned: 2
--         ->  Parallel Seq Scan on session_movie  (cost=0.00..122127.23 rows=16695 width=4)
--               Filter: ((time_start)::date = CURRENT_DATE)
--   ->  Memoize  (cost=0.30..0.33 rows=1 width=37)
--         Cache Key: session_movie.movie_id
--         Cache Mode: logical
--         ->  Index Scan using movie_pkey on movie m  (cost=0.29..0.32 rows=1 width=37)
--               Index Cond: (id = session_movie.movie_id)

-- Оптимизация
-- 1) Чтобы для каждой строки не было преобразование даты фильтр был изменен на "time_start >= CURRENT_DATE
--   AND time_start < CURRENT_DATE + INTERVAL '1 day';"
-- 2) Создан индекс на time_start

CREATE INDEX idx_session_movie_time_start ON session_movie (time_start);

-- Результат
-- Стоимость уменьшилась в 8 раз

-- Nested Loop  (cost=201.68..35448.29 rows=14531 width=33)
--   ->  Bitmap Heap Scan on session_movie  (cost=201.39..34995.71 rows=14531 width=4)
--         Recheck Cond: ((time_start >= CURRENT_DATE) AND (time_start < (CURRENT_DATE + '1 day'::interval)))
--         ->  Bitmap Index Scan on idx_session_movie_time_start  (cost=0.00..197.75 rows=14531 width=0)
--               Index Cond: ((time_start >= CURRENT_DATE) AND (time_start < (CURRENT_DATE + '1 day'::interval)))
--   ->  Memoize  (cost=0.30..0.37 rows=1 width=37)
--         Cache Key: session_movie.movie_id
--         Cache Mode: logical
--         ->  Index Scan using movie_pkey on movie m  (cost=0.29..0.36 rows=1 width=37)
--               Index Cond: (id = session_movie.movie_id)
