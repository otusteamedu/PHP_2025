-- 1) Топ-15 самых больших объектов БД:
--    - таблицы (размер таблицы + все ее индексы)
--    - сами индексы

WITH objects AS (
    SELECT
        n.nspname || '.' || c.relname AS object_name,
        'table_with_indexes'::text AS object_type,
        pg_total_relation_size(c.oid) AS size_bytes
    FROM pg_class c
    JOIN pg_namespace n ON n.oid = c.relnamespace
    WHERE c.relkind = 'r'
      AND n.nspname NOT IN ('pg_catalog', 'information_schema')

    UNION ALL

    SELECT
        n.nspname || '.' || c.relname AS object_name,
        'index'::text AS object_type,
        pg_relation_size(c.oid) AS size_bytes
    FROM pg_class c
    JOIN pg_namespace n ON n.oid = c.relnamespace
    WHERE c.relkind = 'i'
      AND n.nspname NOT IN ('pg_catalog', 'information_schema')
)
SELECT
    object_name,
    object_type,
    pg_size_pretty(size_bytes) AS size_pretty,
    size_bytes
FROM objects
ORDER BY size_bytes DESC
LIMIT 15;


-- 2) Топ-5 часто используемых индексов
SELECT
    s.schemaname,
    s.relname AS table_name,
    s.indexrelname AS index_name,
    s.idx_scan,
    pg_size_pretty(pg_relation_size(s.indexrelid)) AS index_size
FROM pg_stat_user_indexes s
ORDER BY s.idx_scan DESC, pg_relation_size(s.indexrelid) DESC
LIMIT 5;


-- 3) Топ-5 редко используемых индексов
SELECT
    s.schemaname,
    s.relname AS table_name,
    s.indexrelname AS index_name,
    s.idx_scan,
    pg_size_pretty(pg_relation_size(s.indexrelid)) AS index_size
FROM pg_stat_user_indexes s
ORDER BY s.idx_scan ASC, pg_relation_size(s.indexrelid) DESC
LIMIT 5;

-- Подсказка: для чистого замера перед тестом можно сбросить статистику:
-- SELECT pg_stat_reset();
