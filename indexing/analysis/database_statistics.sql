WITH object_sizes AS (
    SELECT
        'table' as object_type,
        schemaname,
        tablename as object_name,
        pg_total_relation_size(schemaname||'.'||tablename) AS size_bytes,
        pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS total_size,
        pg_size_pretty(pg_relation_size(schemaname||'.'||tablename)) AS table_size,
        pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename) - pg_relation_size(schemaname||'.'||tablename)) AS indexes_size
    FROM pg_tables
    WHERE schemaname = 'public'
    UNION ALL
    SELECT
        'index' as object_type,
        schemaname,
        indexname as object_name,
        pg_relation_size(schemaname||'.'||indexname) AS size_bytes,
        pg_size_pretty(pg_relation_size(schemaname||'.'||indexname)) AS total_size,
        '-' as table_size,
        '-' as indexes_size
    FROM pg_indexes
    WHERE schemaname = 'public'
)
SELECT object_type, schemaname, object_name, total_size, table_size, indexes_size
FROM object_sizes
ORDER BY size_bytes DESC
    LIMIT 15;

SELECT
    t.tablename,
    pg_size_pretty(pg_total_relation_size(c.oid)) AS total_size,
    pg_size_pretty(pg_relation_size(c.oid)) AS table_size,
    pg_size_pretty(pg_total_relation_size(c.oid) - pg_relation_size(c.oid)) AS indexes_size,
    pg_total_relation_size(c.oid) as size_bytes
FROM pg_tables t
         JOIN pg_class c ON c.relname = t.tablename
WHERE t.schemaname = 'public'
ORDER BY pg_total_relation_size(c.oid) DESC;

SELECT
    schemaname,
    relname as tablename,
    indexrelname as indexname,
    idx_scan as scans,
    idx_tup_read as tuples_read,
    idx_tup_fetch as tuples_fetched
FROM pg_stat_user_indexes
WHERE schemaname = 'public'
ORDER BY idx_scan DESC
    LIMIT 5;

SELECT
    schemaname,
    relname as tablename,
    indexrelname as indexname,
    idx_scan as scans,
    idx_tup_read as tuples_read,
    idx_tup_fetch as tuples_fetched
FROM pg_stat_user_indexes
WHERE schemaname = 'public'
ORDER BY idx_scan ASC, indexrelname
    LIMIT 5;

SELECT
    schemaname,
    relname as tablename,
    indexrelname as indexname,
    idx_scan,
    idx_tup_read,
    idx_tup_fetch
FROM pg_stat_user_indexes
WHERE schemaname = 'public'
ORDER BY relname, indexrelname;