VACUUM ANALYZE;
SELECT
    coalesce(pg_class.relname, 'all_tables (total_rows)') AS table_name,
    sum(pg_class.reltuples) AS row_count
FROM
    pg_class
JOIN
    pg_namespace
    ON pg_class.relnamespace = pg_namespace.oid
WHERE
    pg_namespace.nspname = 'cinema'
    AND pg_class.relkind = 'r'
GROUP BY
    ROLLUP (pg_class.relname)
ORDER BY
    row_count;
