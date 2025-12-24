SELECT
    tablename AS "Сущность",
    'TABLE' AS "Тип",
    pg_size_pretty(pg_table_size(schemaname||'.'||tablename)) AS "Размер, Мб",
    pg_table_size(schemaname||'.'||tablename) AS size_b
FROM
    pg_tables
WHERE
    schemaname = 'public'
UNION ALL
SELECT
    indexname AS object_name,
    'INDEX' AS object_type,
    pg_size_pretty(pg_table_size(schemaname||'.'||indexname)) AS size_mb,
    pg_table_size(schemaname||'.'||indexname) AS size_b
FROM
    pg_indexes
WHERE
    schemaname = 'public'
ORDER BY
    size_b DESC
LIMIT 15;

SELECT
    indexrelname AS "Индекс",
    relname AS "Таблица",
    idx_scan AS "Сканирований"
FROM
    pg_stat_user_indexes
WHERE
    schemaname = 'public'
ORDER BY
    idx_scan DESC --ASC
LIMIT 5;
