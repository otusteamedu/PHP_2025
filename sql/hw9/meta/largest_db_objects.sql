SELECT
    n.nspname AS schema,
    c.relname AS db_object,
    CASE
        WHEN c.relkind = 'r' THEN 'таблица'
        WHEN c.relkind = 'i' THEN 'индекс'
        WHEN c.relkind = 'S' THEN 'последовательность'
        ELSE 'Другое'
    END AS type,
    pg_size_pretty(pg_total_relation_size(c.oid)) AS size
FROM
    pg_namespace n
JOIN
    pg_class c
    ON n.oid = c.relnamespace
WHERE
    n.nspname = 'cinema'
ORDER BY
    pg_total_relation_size(c.oid) DESC
LIMIT 15;
