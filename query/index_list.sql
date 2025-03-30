-- 5 самых часто используемых индексов
SELECT *
FROM pg_stat_all_indexes
WHERE schemaname = 'public'
ORDER BY idx_scan DESC
LIMIT 5;

-- 5 самых редко используемых индексов
SELECT *
FROM pg_stat_all_indexes
WHERE schemaname = 'public'
ORDER BY idx_scan
LIMIT 5;
;
