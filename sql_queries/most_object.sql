-- Запрос для получения топ 15 самых больших объектов (таблиц, индексов) в базе данных
SELECT 
    n.nspname AS schema_name,
    c.relname AS object_name,
    CASE
        WHEN c.relkind = 'r' THEN 'Table' 
        WHEN c.relkind = 'i' THEN 'Index'
        ELSE 'Other'
    END AS object_type,
    pg_size_pretty(pg_total_relation_size(c.oid)) AS total_size,
    pg_size_pretty(pg_table_size(c.oid)) AS table_size,
    pg_size_pretty(pg_indexes_size(c.oid)) AS index_size
FROM 
    pg_catalog.pg_class c
    LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
WHERE 
    c.relkind IN ('r', 'i')  -- 'r' для таблиц, 'i' для индексов
    AND n.nspname NOT IN ('pg_catalog', 'information_schema')  -- Исключаем системные схемы
ORDER BY 
    pg_total_relation_size(c.oid) DESC
LIMIT 15;



-- Запрос для получения топ 5 самых часто используемых индексов
SELECT 
    idx.relname AS index_name,
    t.relname AS table_name,
    idx.idx_scan AS index_usage_count,
    pg_size_pretty(pg_relation_size(idx.indexrelid)) AS index_size
FROM 
    pg_stat_user_indexes idx
    JOIN pg_class t ON idx.relid = t.oid
WHERE 
    t.relkind = 'r'  -- Только для таблиц
    AND idx.idx_scan > 0  -- Исключаем неиспользуемые индексы
ORDER BY 
    idx.idx_scan DESC
LIMIT 5;

-- Запрос для получения топ 5 самых редко используемых индексов
SELECT 
    idx.relname AS index_name,
    t.relname AS table_name,
    idx.idx_scan AS index_usage_count,
    pg_size_pretty(pg_relation_size(idx.indexrelid)) AS index_size
FROM 
    pg_stat_user_indexes idx
    JOIN pg_class t ON idx.relid = t.oid
WHERE 
    t.relkind = 'r'  -- Только для таблиц
    AND idx.idx_scan = 0  -- Индексы, которые не использовались
ORDER BY 
    idx.idx_scan ASC
LIMIT 5;
