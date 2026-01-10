- 15 самых больших объектов (таблицы и индексы)
SELECT
    tablename,
    'table' AS object_type,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS total_size_pretty,
    pg_total_relation_size(schemaname||'.'||tablename) AS total_size
FROM pg_tables
WHERE schemaname NOT IN ('pg_catalog', 'information_schema')
UNION 
SELECT
    tablename,
    'index' AS object_type,
    pg_size_pretty(pg_relation_size(schemaname||'.'||indexname)) AS total_size_pretty,
    pg_relation_size(schemaname||'.'||indexname) AS total_size
FROM pg_indexes
WHERE schemaname NOT IN ('pg_catalog', 'information_schema')
ORDER BY total_size DESC
LIMIT 15;

-- 5 самых часто используемых индексов
SELECT
    schemaname,
    relname AS tablename,
    indexrelname AS indexname,
    idx_scan,
    pg_size_pretty(pg_relation_size(indexrelid)) AS index_size
FROM pg_stat_user_indexes
ORDER BY idx_scan DESC
LIMIT 5;

-- 5 самых редко используемых индексов (включая никогда не использовавшиеся)
SELECT
    schemaname,
    relname AS tablename,
    indexrelname AS indexname,
    idx_scan,
    pg_size_pretty(pg_relation_size(indexrelid)) AS index_size
FROM pg_stat_user_indexes
ORDER BY idx_scan ASC
LIMIT 5;