WITH
    table_sizes AS (
        SELECT
            schemaname AS schema_name,
            relname AS entity_name,
            pg_size_pretty(pg_total_relation_size(relid)) AS readable_size,
            pg_total_relation_size(relid) AS raw_size
        FROM
            pg_catalog.pg_statio_user_tables
    ),
    index_sizes AS (
        SELECT
            n.nspname AS schema_name,
            c.relname AS entity_name,
            pg_size_pretty(pg_relation_size(c.oid)) AS readable_size,
            pg_relation_size(c.oid) AS raw_size
        FROM
            pg_catalog.pg_index i
                JOIN pg_catalog.pg_class c ON c.oid = i.indexrelid
                JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
    )
SELECT
    ('"' || schema_name || '"."' || entity_name || '"') AS entity_name,
    readable_size
FROM (
         SELECT * FROM table_sizes
         UNION ALL
         SELECT * FROM index_sizes
     ) AS all_sizes
ORDER BY
    raw_size DESC
    LIMIT 15;

+---------------------------------+-------------+
|entity_name                      |readable_size|
+---------------------------------+-------------+
|"public"."tickets"               |1468 MB      |
|"public"."session"               |1234 MB      |
|"public"."rooms"                 |1103 MB      |
|"public"."films"                 |1020 MB      |
|"public"."clients"               |945 MB       |
|"public"."orders"                |869 MB       |
|"public"."idx-tickets-price"     |215 MB       |
|"public"."session_pkey"          |214 MB       |
|"public"."rooms_pkey"            |214 MB       |
|"public"."tickets_pkey"          |214 MB       |
|"public"."orders_pkey"           |214 MB       |
|"public"."clients_pkey"          |214 MB       |
|"public"."films_pkey"            |214 MB       |
|"public"."idx-sessions-film_id"  |188 MB       |
|"public"."idx-tickets-session_id"|188 MB       |
+---------------------------------+-------------+
