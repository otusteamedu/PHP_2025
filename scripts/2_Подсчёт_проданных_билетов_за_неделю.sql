EXPLAIN SELECT count(*) FROM ticket
WHERE created_at::date > CURRENT_DATE - 7 AND status='куплен';

-- 10.000 записей
-- Aggregate  (cost=292.33..292.34 rows=1 width=8)
--  ->  Seq Scan on ticket  (cost=0.00..284.00 rows=3333 width=0)
--       Filter: ((created_at)::date > (CURRENT_DATE - 7))

-- 10.000.000 записей
-- Finalize Aggregate  (cost=179977.88..179977.89 rows=1 width=8)
--   ->  Gather  (cost=179977.66..179977.87 rows=2 width=8)
--         Workers Planned: 2
--         ->  Partial Aggregate  (cost=178977.66..178977.67 rows=1 width=8)
--               ->  Parallel Seq Scan on ticket  (cost=0.00..177042.50 rows=774065 width=0)
--                     Filter: (((status)::text = 'куплен'::text) AND (created_at > (now() - '7 days'::interval)))
-- JIT:
--   Functions: 6
-- "  Options: Inlining false, Optimization false, Expressions true, Deforming true"
-- Execution Time: 459.499 ms

-- Оптимизация
-- 1) Замена условия с "created_at::date > CURRENT_DATE - 7;" на created_at > NOW() - INTERVAL '7 days';
--    так как в случае created_at::date дата будет конвертироваться для каждой строчки в нужный формат
-- 2) Если исходить из того что в приложении будет часто использована фильтр по полям "created_at, status"
--    создам составной индекс "idx_ticket_created_at_status".

CREATE INDEX idx_ticket_created_at_status ON ticket (created_at, status);

-- Результат
-- Finalize Aggregate  (cost=64418.21..64418.22 rows=1 width=8)
--   ->  Gather  (cost=64418.00..64418.21 rows=2 width=8)
--         Workers Planned: 2
--         ->  Partial Aggregate  (cost=63418.00..63418.01 rows=1 width=8)
--               ->  Parallel Index Only Scan using idx_ticket_created_at_status on ticket  (cost=0.44..61482.84 rows=774062 width=0)
--                     Index Cond: ((created_at > (now() - '7 days'::interval)) AND (status = 'куплен'::text))

-- Execution Time: 133.121 ms

-- Время выполнения уменьшилось ~ в 3 раза
-- Стоимость уменьшилась ~ в 3 раза