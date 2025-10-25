-- Отсортированный список (15 значений) самых больших по размеру объектов БД (таблицы, включая индексы, сами индексы)
WITH
    table_sizes AS (
        SELECT
            schemaname AS schema_name,
            relname AS object_name,
            pg_size_pretty(pg_total_relation_size(relid)) AS size,
            pg_total_relation_size(relid) AS size_bytes
        FROM
            pg_catalog.pg_statio_user_tables
    ),
    index_sizes AS (
        SELECT
            n.nspname AS schema_name,
            c.relname AS object_name,
            pg_size_pretty(pg_relation_size(c.oid)) AS size,
            pg_relation_size(c.oid) AS size_bytes
        FROM
            pg_catalog.pg_index i
            JOIN pg_catalog.pg_class c ON c.oid = i.indexrelid
            JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
     )
SELECT
    ('"' || schema_name || '"."' || object_name || '"') AS object_name,
    size
FROM (
         SELECT * FROM table_sizes
         UNION ALL
         SELECT * FROM index_sizes
     ) AS all_sizes
ORDER BY
    size_bytes DESC
LIMIT 15;

/*
object_name                     |size   |
--------------------------------+-------+
"public"."movies"               |1033 MB|
"public"."clients"              |968 MB |
"public"."tickets"              |745 MB |
"public"."seat_prices"          |590 MB |
"public"."sessions"             |553 MB |
"public"."seats"                |490 MB |
"public"."movies_name_key"      |481 MB |
"public"."orders"               |461 MB |
"public"."clients_email_key"    |260 MB |
"public"."idx-tickets-price"    |107 MB |
"public"."idx-seat_prices-price"|107 MB |
"public"."orders_pkey"          |107 MB |
"public"."clients_pkey"         |107 MB |
"public"."movies_pkey"          |107 MB |
"public"."sessions_pkey"        |107 MB |
*/
