-- =============================================================================
-- Q1: Какие фильмы идут сегодня
-- =============================================================================
-- Выбор всех фильмов (по id) из расписания на текущую дату
-- Тип: простой (1 таблица, DISTINCT + сортировка)

-- ██  ЗАПРОС  █████████████████████████████████████████████████████████████████

SELECT DISTINCT s.movie_id
FROM public.screenings s
WHERE s.start_time >= CURRENT_DATE
  AND s.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY s.movie_id;

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 строк  ████████████████████████████████

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT DISTINCT s.movie_id
FROM public.screenings s
WHERE s.start_time >= CURRENT_DATE
  AND s.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY s.movie_id;

-- План:
-- Sort  (cost=5.00..5.01 rows=3 width=4) (actual time=0.142..0.144 rows=3 loops=1)
--   Output: movie_id
--   Sort Key: s.movie_id
--   Sort Method: quicksort  Memory: 25kB
--   Buffers: shared hit=5
--   ->  HashAggregate  (cost=4.95..4.98 rows=3 width=4) (actual time=0.099..0.100 rows=3 loops=1)
--         Output: movie_id
--         Group Key: s.movie_id
--         Batches: 1  Memory Usage: 24kB
--         Buffers: shared hit=2
--         ->  Seq Scan on public.screenings s  (cost=0.00..4.90 rows=18 width=4) (actual time=0.021..0.077 rows=18 loops=1)
--               Output: id, movie_id, hall_id, start_time, end_time, type, language, subtitles, is_active
--               Filter: ((s.start_time >= CURRENT_DATE) AND (s.start_time < (CURRENT_DATE + '1 day'::interval)))
--               Rows Removed by Filter: 111
--               Buffers: shared hit=2
-- Planning Time: 1.269 ms
-- Execution Time: 0.310 ms

-- Вывод: Seq Scan — читает всю таблицу (129 строк), фильтрует. Для 10K это нормально.

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 000 строк  ████████████████████████████

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT DISTINCT s.movie_id
FROM public.screenings s
WHERE s.start_time >= CURRENT_DATE
  AND s.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY s.movie_id;

-- План:
-- Sort  (cost=12.07..12.17 rows=10 width=4) (actual time=0.090..0.091 rows=10 loops=1)
--   Output: movie_id
--   Sort Key: s.movie_id
--   Sort Method: quicksort  Memory: 25kB
--   Buffers: shared hit=16 read=2
--   ->  HashAggregate  (cost=12.07..12.17 rows=10 width=4) (actual time=0.090..0.091 rows=10 loops=1)
--         Output: movie_id
--         Group Key: s.movie_id
--         Batches: 1  Memory Usage: 24kB
--         Buffers: shared hit=13 read=2
--         ->  Index Scan using idx_start_time on public.screenings s  (cost=0.30..11.76 rows=123 width=4) (actual time=0.048..0.068 rows=125 loops=1)
--               Output: id, movie_id, hall_id, start_time, end_time, type, language, subtitles, is_active
--               Index Cond: ((s.start_time >= CURRENT_DATE) AND (s.start_time < (CURRENT_DATE + '1 day'::interval)))
--               Buffers: shared hit=13 read=2
-- Planning Time: 0.610 ms
-- Execution Time: 0.196 ms

-- Вывод: Index Scan по idx_start_time — уже быстрее, но каждый entry → heap fetch.
-- Нужен покрывающий индекс, чтобы не ходить в таблицу.

-- ██  ПРЕДЛАГАЕМЫЕ УЛУЧШЕНИЯ  ████████████████████████████████████████████████

-- 1) Создать покрывающий индекс (covering index), включающий start_time, hall_id, movie_id.
--    Это позволяет делать Index Only Scan — без обращений к таблице (heap).
CREATE INDEX IF NOT EXISTS idx_screenings_start_time_hall_movie
    ON public.screenings (start_time, hall_id, movie_id);

-- 2) Обновить статистику, чтобы планировщик знал реальные размеры таблиц.
ANALYZE public.screenings;

-- ██  EXPLAIN: ПОСЛЕ ОПТИМИЗАЦИИ — 10 000 000 строк  █████████████████████████

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT DISTINCT s.movie_id
FROM public.screenings s
WHERE s.start_time >= CURRENT_DATE
  AND s.start_time < CURRENT_DATE + INTERVAL '1 day'
ORDER BY s.movie_id;

-- План:
-- Sort  (cost=7.15..7.25 rows=10 width=4) (actual time=0.121..0.122 rows=10 loops=1)
--   Output: movie_id
--   Sort Key: s.movie_id
--   Sort Method: quicksort  Memory: 25kB
--   Buffers: shared hit=7 read=3
--   ->  HashAggregate  (cost=7.15..7.25 rows=10 width=4) (actual time=0.121..0.122 rows=10 loops=1)
--         Output: movie_id
--         Group Key: s.movie_id
--         Batches: 1  Memory Usage: 24kB
--         Buffers: shared hit=4 read=3
--         ->  Index Only Scan using idx_screenings_start_time_hall_movie on public.screenings s  (cost=0.30..6.84 rows=127 width=4) (actual time=0.067..0.100 rows=125 loops=1)
--               Output: start_time, hall_id, movie_id
--               Index Cond: ((s.start_time >= CURRENT_DATE) AND (s.start_time < (CURRENT_DATE + '1 day'::interval)))
--               Heap Fetches: 0
--               Buffers: shared hit=4 read=3
-- Planning Time: 1.063 ms
-- Execution Time: 0.238 ms

-- Вывод: Index Only Scan — heap fetches = 0, буферов меньше (7 против 18).
-- Execution Time 0.238 ms vs 0.196 ms до — разница незначительна на малой выборке,
-- но при реальной нагрузке снимаем чтение таблицы.
