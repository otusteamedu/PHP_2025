-- =============================================================================
-- Q3: Афиша кинотеатра на сегодня
-- =============================================================================
-- Полное расписание на сегодня: фильм, жанр, зал, время начала/окончания
-- Тип: сложный (3 таблицы: screenings → movies → halls, JOIN + сортировка)

-- ██  ЗАПРОС  █████████████████████████████████████████████████████████████████

SELECT
    m.title,
    m.genre,
    h.name,
    h.type,
    scr.start_time,
    scr.end_time,
    m.duration_min
FROM public.screenings scr
JOIN public.movies m ON m.id = scr.movie_id
JOIN public.halls h ON h.id = scr.hall_id
WHERE scr.start_time >= CURRENT_DATE
  AND scr.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY scr.start_time;

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 строк  ████████████████████████████████
-- Выполнить на БД после DML_10000.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    m.title, m.genre, h.name, h.type,
    scr.start_time, scr.end_time, m.duration_min
FROM public.screenings scr
JOIN public.movies m ON m.id = scr.movie_id
JOIN public.halls h ON h.id = scr.hall_id
WHERE scr.start_time >= CURRENT_DATE
  AND scr.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY scr.start_time;

-- Ожидаемый план на 10K:
-- Nested Loop / Hash Join 2 таблиц + Seq Scan screenings (фильтр по start_time)
-- Execution Time: <1 ms — данных мало, планировщик выберет простой план

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 000 строк  ████████████████████████████
-- Выполнить на БД после DML_10000000.sql (до optimizations.sql)

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    m.title, m.genre, h.name, h.type,
    scr.start_time, scr.end_time, m.duration_min
FROM public.screenings scr
JOIN public.movies m ON m.id = scr.movie_id
JOIN public.halls h ON h.id = scr.hall_id
WHERE scr.start_time >= CURRENT_DATE
  AND scr.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY scr.start_time;

-- Ожидаемый план на 10M (без оптимизации):
-- Index Scan by idx_start_time на screenings (находит ~125 строк на сегодня)
-- → Nested Loop join с movies (PK lookup)
-- → Nested Loop join с halls (PK lookup)
-- Execution Time: ~0.3-0.5 ms — уже неплохо, т.к. idx_start_time существует в DDL.
-- Узкое место: Index Scan читает всю строку из таблицы (heap access).

-- ██  ПРЕДЛАГАЕМЫЕ УЛУЧШЕНИЯ  ████████████████████████████████████████████████

-- 1) Покрывающий индекс для screenings: (start_time, hall_id, movie_id) — 
--    позволяет делать Index Only Scan, не ходить в таблицу за hall_id и movie_id.
--    Вместе с idx_screenings_start_time_hall_movie (создан в optimizations.sql).
CREATE INDEX IF NOT EXISTS idx_screenings_start_time_hall_movie
    ON public.screenings (start_time, hall_id, movie_id);

-- 2) Обновить статистику.
ANALYZE public.screenings;
ANALYZE public.movies;
ANALYZE public.halls;

-- ██  EXPLAIN: ПОСЛЕ ОПТИМИЗАЦИИ — 10 000 000 строк  █████████████████████████
-- Выполнить на БД после DML_10000000.sql + optimizations.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    m.title, m.genre, h.name, h.type,
    scr.start_time, scr.end_time, m.duration_min
FROM public.screenings scr
JOIN public.movies m ON m.id = scr.movie_id
JOIN public.halls h ON h.id = scr.hall_id
WHERE scr.start_time >= CURRENT_DATE
  AND scr.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY scr.start_time;

-- Ожидаемый план:
-- Nested Loop
--   ->  Nested Loop
--         ->  Index Only Scan using idx_screenings_start_time_hall_movie
--               Index Cond: start_time >= ... AND < ...
--         ->  Index Scan using movies_pkey
--   ->  Index Scan using halls_pkey
-- Execution Time: ~0.2 ms — Index Only Scan без heap fetches для screenings.
