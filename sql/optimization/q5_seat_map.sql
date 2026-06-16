-- =============================================================================
-- Q5: Схема зала (свободные/занятые места) для конкретного сеанса
-- =============================================================================
-- Показывает все места в зале с пометкой free/occupied на основе проданных/забронированных билетов
-- Тип: сложный (3 таблицы: seats → screenings → tickets, LEFT JOIN + подзапрос)

-- ██  ЗАПРОС  █████████████████████████████████████████████████████████████████

\set screening_id 1

SELECT
    s.row_num,
    s.seat_num,
    s.seat_type,
    CASE WHEN t.id IS NULL THEN 'free' ELSE 'occupied' END AS seat_status
FROM public.seats s
JOIN public.screenings scr
  ON scr.id = :screening_id
 AND scr.hall_id = s.hall_id
LEFT JOIN public.tickets t
  ON t.screening_id = scr.id
 AND t.seat_id = s.id
 AND t.status IN ('sold', 'reserved')
ORDER BY s.row_num, s.seat_num;

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 строк  ████████████████████████████████
-- Выполнить на БД после DML_10000.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    s.row_num, s.seat_num, s.seat_type,
    CASE WHEN t.id IS NULL THEN 'free' ELSE 'occupied' END AS seat_status
FROM public.seats s
JOIN public.screenings scr
  ON scr.id = 1
 AND scr.hall_id = s.hall_id
LEFT JOIN public.tickets t
  ON t.screening_id = scr.id
 AND t.seat_id = s.id
 AND t.status IN ('sold', 'reserved')
ORDER BY s.row_num, s.seat_num;

-- Ожидаемый план на 10K:
-- Nested Loop LEFT JOIN с фильтрацией по screening_id = 1 и status IN (...)
-- Seq Scan seats + фильтр по hall_id
-- Execution Time: <1 ms — данных мало

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 000 строк  ████████████████████████████
-- Выполнить на БД после DML_10000000.sql (до optimizations.sql)

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    s.row_num, s.seat_num, s.seat_type,
    CASE WHEN t.id IS NULL THEN 'free' ELSE 'occupied' END AS seat_status
FROM public.seats s
JOIN public.screenings scr
  ON scr.id = 1
 AND scr.hall_id = s.hall_id
LEFT JOIN public.tickets t
  ON t.screening_id = scr.id
 AND t.seat_id = s.id
 AND t.status IN ('sold', 'reserved')
ORDER BY s.row_num, s.seat_num;

-- Ожидаемый план на 10M (без оптимизации):
-- Nested Loop
--   ->  Index Scan on screenings_pkey (screening_id = 1)
--   ->  Index Scan on idx_hall_id (seats по hall_id)
--   ->  Index Scan on idx_screening_id (tickets по screening_id) + filter по status
-- Проблема: фильтр t.status IN ('sold','reserved') применяется после сканирования
-- idx_screening_id — читает все билеты сеанса (включая cancelled, refunded) и отсеивает.
-- Для сеанса мест ~100, билетов ~80, overhead небольшой.
-- Но индекс idx_screening_id не покрывает seat_id → heap lookup.

-- ██  ПРЕДЛАГАЕМЫЕ УЛУЧШЕНИЯ  ████████████████████████████████████████████████

-- 1) Частичный индекс по (screening_id, seat_id) WHERE status IN ('sold','reserved').
--    Покрывает LEFT JOIN-условие: по screening_id находим seat_id занятых мест.
--    Index Only Scan — не ходим в таблицу.
CREATE INDEX IF NOT EXISTS idx_tickets_screening_seat_occupied
    ON public.tickets (screening_id, seat_id)
    WHERE status IN ('sold', 'reserved');

-- 2) Обновить статистику.
ANALYZE public.tickets;
ANALYZE public.seats;

-- ██  EXPLAIN: ПОСЛЕ ОПТИМИЗАЦИИ — 10 000 000 строк  █████████████████████████
-- Выполнить на БД после DML_10000000.sql + optimizations.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT
    s.row_num, s.seat_num, s.seat_type,
    CASE WHEN t.id IS NULL THEN 'free' ELSE 'occupied' END AS seat_status
FROM public.seats s
JOIN public.screenings scr
  ON scr.id = 1
 AND scr.hall_id = s.hall_id
LEFT JOIN public.tickets t
  ON t.screening_id = scr.id
 AND t.seat_id = s.id
 AND t.status IN ('sold', 'reserved')
ORDER BY s.row_num, s.seat_num;

-- Ожидаемый план:
-- Nested Loop Left Join
--   ->  Nested Loop
--         ->  Index Scan on screenings_pkey (screening_id = 1)
--         ->  Index Scan using idx_hall_id on seats (hall_id = scr.hall_id)
--   ->  Index Only Scan using idx_tickets_screening_seat_occupied
--         Index Cond: (screening_id = 1) AND (seat_id = s.id)
--         Heap Fetches: 0
-- Execution Time: <1 ms — частичный индекс читает только sold/reserved, без лишних строк.
