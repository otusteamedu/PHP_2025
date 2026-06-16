-- =============================================================================
-- Q4: Топ-3 самых прибыльных фильмов за неделю
-- =============================================================================
-- Выручка по каждому фильму, сортировка, лимит 3
-- Тип: сложный (3 таблицы: tickets → screenings → movies, JOIN + GROUP BY + FILTER)

-- ██  ЗАПРОС  █████████████████████████████████████████████████████████████████

SELECT
    m.title,
    COUNT(*) FILTER (WHERE t.status = 'sold') AS sold_tickets,
    SUM(t.price) FILTER (WHERE t.status = 'sold') AS revenue
FROM public.tickets t
JOIN public.screenings scr ON scr.id = t.screening_id
JOIN public.movies m ON m.id = scr.movie_id
WHERE scr.start_time >= CURRENT_DATE - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY revenue DESC NULLS LAST
LIMIT 3;

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 строк  ████████████████████████████████
-- Выполнить на БД после DML_10000.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    m.title,
    COUNT(*) FILTER (WHERE t.status = 'sold') AS sold_tickets,
    SUM(t.price) FILTER (WHERE t.status = 'sold') AS revenue
FROM public.tickets t
JOIN public.screenings scr ON scr.id = t.screening_id
JOIN public.movies m ON m.id = scr.movie_id
WHERE scr.start_time >= CURRENT_DATE - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY revenue DESC NULLS LAST
LIMIT 3;

-- Ожидаемый план на 10K:
-- 3× Hash Join / Nested Loop + Seq Scan на tickets + фильтр по status
-- Execution Time: ~2-5 ms — для маленькой БД норм

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 000 строк  ████████████████████████████
-- Выполнить на БД после DML_10000000.sql (до optimizations.sql)

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    m.title,
    COUNT(*) FILTER (WHERE t.status = 'sold') AS sold_tickets,
    SUM(t.price) FILTER (WHERE t.status = 'sold') AS revenue
FROM public.tickets t
JOIN public.screenings scr ON scr.id = t.screening_id
JOIN public.movies m ON m.id = scr.movie_id
WHERE scr.start_time >= CURRENT_DATE - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY revenue DESC NULLS LAST
LIMIT 3;

-- Ожидаемый план на 10M (без оптимизации):
-- Parallel Seq Scan на tickets (~10M строк) — из них ~88% sold,  остальное filter out.
-- Hash Join с screenings по screening_id
-- Hash Join с movies по movie_id
-- GROUP BY, сортировка, LIMIT 3
-- Execution Time: >500-1000 ms — плохо: читаются все tickets, хотя нужны только sold
-- за последнюю неделю.

-- ██  ПРЕДЛАГАЕМЫЕ УЛУЧШЕНИЯ  ████████████████████████████████████████████████

-- 1) Частичный индекс только по sold-билетам: (screening_id, price) WHERE status = 'sold'.
--    Для Q4 нужно:
--      - JOIN по screening_id → фильм
--      - SUM(price) — агрегат по цене
--    Индекс покрывает оба поля, даёт Index Only Scan.
CREATE INDEX IF NOT EXISTS idx_tickets_sold_screening_price
    ON public.tickets (screening_id, price)
    WHERE status = 'sold';

-- 2) Обновить статистику.
ANALYZE public.tickets;
ANALYZE public.screenings;

-- ██  EXPLAIN: ПОСЛЕ ОПТИМИЗАЦИИ — 10 000 000 строк  █████████████████████████
-- Выполнить на БД после DML_10000000.sql + optimizations.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    m.title,
    COUNT(*) FILTER (WHERE t.status = 'sold') AS sold_tickets,
    SUM(t.price) FILTER (WHERE t.status = 'sold') AS revenue
FROM public.tickets t
JOIN public.screenings scr ON scr.id = t.screening_id
JOIN public.movies m ON m.id = scr.movie_id
WHERE scr.start_time >= CURRENT_DATE - INTERVAL '7 days'
GROUP BY m.id, m.title
ORDER BY revenue DESC NULLS LAST
LIMIT 3;

-- Ожидаемый план:
-- Limit
--   ->  Sort (revenue DESC)
--         ->  HashAggregate (GROUP BY m.id)
--               ->  Nested Loop
--                     ->  Nested Loop
--                           ->  Index Scan using idx_screenings_start_time_hall_movie
--                                 Index Cond: start_time >= 7 days ago
--                           ->  Index Only Scan using idx_tickets_sold_screening_price
--                                 Index Cond: (screening_id = scr.id)
--                     ->  Index Scan using movies_pkey
-- Execution Time: <50 ms — индекс фильтрует sold-билеты, второй индекс — по дате сеансов.
