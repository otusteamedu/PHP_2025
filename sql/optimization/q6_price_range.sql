-- =============================================================================
-- Q6: Минимальная и максимальная цена билета на конкретный сеанс
-- =============================================================================
-- Диапазон цен на конкретный сеанс (MIN/MAX price по screening_id)
-- Тип: простой (1 таблица, MIN/MAX aggregate с GROUP BY screening_id)

-- ██  ЗАПРОС  █████████████████████████████████████████████████████████████████

\set screening_id 1

SELECT MIN(t.price) AS min_price, MAX(t.price) AS max_price
FROM public.tickets t
WHERE t.screening_id = :screening_id;

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 строк  ████████████████████████████████
-- Выполнить на БД после DML_10000.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT MIN(t.price) AS min_price, MAX(t.price) AS max_price
FROM public.tickets t
WHERE t.screening_id = 1;

-- Ожидаемый план на 10K:
-- Aggregate
--   ->  Seq Scan on tickets (filter: screening_id = 1)
-- Execution Time: <1 ms — таблица маленькая, всё ок

-- ██  EXPLAIN: ДО ОПТИМИЗАЦИИ — 10 000 000 строк  ████████████████████████████
-- Выполнить на БД после DML_10000000.sql (до optimizations.sql)

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT MIN(t.price) AS min_price, MAX(t.price) AS max_price
FROM public.tickets t
WHERE t.screening_id = 1;

-- Ожидаемый план на 10M (без оптимизации):
-- Aggregate
--   ->  Index Scan using idx_screening_id
--         Index Cond: (screening_id = 1)
-- Проблема: idx_screening_id есть, но он не включает price.
-- После нахождения строк по screening_id → heap lookup за price.
-- Для screening_id = 1 будет ~80-100 строк — overhead невелик,
-- но при частых вызовах множества screening_id это умножается.

-- ██  ПРЕДЛАГАЕМЫЕ УЛУЧШЕНИЯ  ████████████████████████████████████████████████

-- 1) Покрывающий индекс (screening_id, price) — позволяет делать
--    Index Only Scan для MIN/MAX сразу по индексу, без heap access.
CREATE INDEX IF NOT EXISTS idx_tickets_screening_price
    ON public.tickets (screening_id, price);

-- 2) Обновить статистику.
ANALYZE public.tickets;

-- ██  EXPLAIN: ПОСЛЕ ОПТИМИЗАЦИИ — 10 000 000 строк  █████████████████████████
-- Выполнить на БД после DML_10000000.sql + optimizations.sql

EXPLAIN (ANALYZE, BUFFERS, VERBOSE)
SELECT MIN(t.price) AS min_price, MAX(t.price) AS max_price
FROM public.tickets t
WHERE t.screening_id = 1;

-- Ожидаемый план:
-- Aggregate
--   ->  Index Only Scan using idx_tickets_screening_price
--         Index Cond: (screening_id = 1)
--         Heap Fetches: 0
-- Execution Time: <0.1 ms — MIN/MAX читают первую и последнюю запись
-- в B-Tree по (screening_id, price), сканирования не требуется.
