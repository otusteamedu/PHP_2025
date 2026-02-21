SELECT
    schemaname AS schema,
    relname AS "table",
    indexrelname AS index,
    idx_scan AS scans,
    idx_tup_read AS tuples_read,
    idx_tup_fetch AS tuples_fetch
FROM
    pg_stat_user_indexes
ORDER BY
    idx_scan DESC
LIMIT 5;
