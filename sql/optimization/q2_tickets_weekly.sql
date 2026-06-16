-- =============================================================================
-- Q2: Сколько билетов продано за неделю
-- =============================================================================
-- Подсчёт количества проданных билетов за последние 7 дней
-- Тип: простой (1 таблица, COUNT + фильтр по status и purchase_time)

-- ██  ЗАПРОС  █████████████████████████████████████████████████████████████████

SELECT COUNT(*) AS sold_tickets_last_7_days
FROM public.tickets t
WHERE t.status = 'sold'
  AND t.purchase_time >= CURRENT_DATE - INTERVAL '7 days';

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 строк  ████████████████████████████████
-- Выполнить на БД после DML_10000.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT COUNT(*) AS sold_tickets_last_7_days
FROM public.tickets t
WHERE t.status = 'sold'
  AND t.purchase_time >= CURRENT_DATE - INTERVAL '7 days';

-- Ожидаемый план на 10K:
-- Seq Scan — полное сканирование ~10 000 строк tickets с фильтром по статусу и дате.
-- Фильтр: t.status = 'sold' AND t.purchase_time >= CURRENT_DATE - INTERVAL '7 days'
-- Execution Time: ~1-2 ms

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 000 строк  ████████████████████████████
-- Выполнить на БД после DML_10000000.sql (до запуска optimizations.sql)

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT COUNT(*) AS sold_tickets_last_7_days
FROM public.tickets t
WHERE t.status = 'sold'
  AND t.purchase_time >= CURRENT_DATE - INTERVAL '7 days';

-- Ожидаемый план на 10M (без оптимизации):
-- Parallel Seq Scan — параллельное сканирование ~10 000 000 строк.
-- Фильтр по статусу отсеивает ~88% строк (status != 'sold'), затем фильтр по дате.
-- Execution Time: >300 ms (оценивается) — плохо: фильтр по статусу неселективный,
-- и дата тоже заставляет читать много строк.

-- ██  ПРЕДЛАГАЕМЫЕ УЛУЧШЕНИЯ  ████████████████████████████████████████████████

-- 1) Частичный (partial) индекс только по проданным билетам и purchase_time.
--    Индекс маленький (~64 MB против 214 MB полного), содержит только sold,
--    и для Q2 достаточно Index Only Scan по purchase_time.
CREATE INDEX IF NOT EXISTS idx_tickets_sold_purchase_time
    ON public.tickets (purchase_time)
    WHERE status = 'sold';

-- 2) Обновить статистику.
ANALYZE public.tickets;

-- ██  EXPLAIN: ПОСЛЕ ОПТИМИЗАЦИИ — 10 000 000 строк  █████████████████████████
-- Выполнить на БД после DML_10000000.sql + optimizations.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT COUNT(*) AS sold_tickets_last_7_days
FROM public.tickets t
WHERE t.status = 'sold'
  AND t.purchase_time >= CURRENT_DATE - INTERVAL '7 days';

-- Ожидаемый план:
-- Aggregate
--   ->  Index Only Scan using idx_tickets_sold_purchase_time
--         Index Cond: (purchase_time >= CURRENT_DATE - INTERVAL '7 days')
--         Heap Fetches: 0
-- Execution Time: <10 ms — индекс читает только sold-строки нужного периода,
-- без фильтрации и без обращений к таблице.
