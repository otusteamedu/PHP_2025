-- Отсортированный список (15 значений) самых больших по размеру объектов БД (таблицы, включая индексы, сами индексы):

WITH table_sizes AS (SELECT schemaname                                    AS schema_name,
                            relname                                       AS object_name,
                            pg_size_pretty(pg_total_relation_size(relid)) AS size,
                            pg_total_relation_size(relid)                 AS size_bytes
FROM pg_catalog.pg_statio_user_tables),
     index_sizes AS (SELECT c_pg_namespace.nspname                           AS schema_name,
                            c_pg_class.relname                               AS object_name,
                            pg_size_pretty(pg_relation_size(c_pg_class.oid)) AS size,
                            pg_relation_size(c_pg_class.oid)                 AS size_bytes
                     FROM pg_catalog.pg_index i
                              JOIN pg_catalog.pg_class AS c_pg_class ON c_pg_class.oid = i.indexrelid
                              JOIN pg_catalog.pg_namespace AS c_pg_namespace
                                   ON c_pg_namespace.oid = c_pg_class.relnamespace)
SELECT ('"' || schema_name || '"."' || object_name || '"') AS object_name,
       size
FROM (SELECT *
      FROM table_sizes
      UNION ALL
      SELECT *
      FROM index_sizes) AS all_sizes
ORDER BY size_bytes DESC
LIMIT 15;

/*
|:--------------------------------------|:--------|
| object_name                           | size    |
|:--------------------------------------|:--------|
| "public"."films"                      | 4126 MB |
| "public"."clients"                    | 1784 MB |
| "public"."tickets"                    | 1492 MB |
| "public"."price_list"                 | 1181 MB |
| "public"."sessions"                   | 1103 MB |
| "public"."orders "                    | 999 MB  |
| "public"."places"                     | 977 MB  |
| "public"."clients_email_key"          | 681 MB  |
| "public"."idx-price_list-price"       | 215 MB  |
| "public"."idx-tickets-price"          | 215 MB  |
| "public"."clients_pkey"               | 214 MB  |
| "public"."films_pkey"                 | 214 MB  |
| "public"."places_pkey"                | 214 MB  |
| "public"."price_list_pkey"            | 214 MB  |
| "public"."orders_pkey"                | 214 MB  |
|:--------------------------------------|:--------|
*/
