-- 5 самых часто используемых индексов
SELECT
    *
FROM
    pg_stat_all_indexes
WHERE
    schemaname = 'public'
ORDER BY
    idx_scan DESC
LIMIT 5;

/*
relid|indexrelid|schemaname|relname        |indexrelname        |idx_scan|last_idx_scan                |idx_tup_read|idx_tup_fetch|
-----+----------+----------+---------------+--------------------+--------+-----------------------------+------------+-------------+
19446|     19450|public    |sessions       |sessions_pkey       |10202458|2025-03-18 18:14:41.098 +0300|    10202458|     10202433|
19428|     19432|public    |halls          |halls_pkey          |10000114|2025-03-18 18:14:41.098 +0300|    10000114|     10000000|
19437|     19441|public    |seat_categories|seat_categories_pkey|10000002|2025-03-18 14:21:19.104 +0300|    10000002|     10000002|
19419|     19423|public    |movies         |movies_pkey         | 5495913|2025-03-18 18:14:22.400 +0300|     5495913|      5495786|
19406|     19411|public    |orders         |orders_pkey         | 5000141|2025-03-18 18:14:41.098 +0300|     5000141|      5000018|
*/

-- 5 самых редко используемых индексов
SELECT
    *
FROM
    pg_stat_all_indexes
WHERE
    schemaname = 'public'
ORDER BY
    idx_scan
LIMIT 5;

/*
relid|indexrelid|schemaname|relname        |indexrelname            |idx_scan|last_idx_scan|idx_tup_read|idx_tup_fetch|
-----+----------+----------+---------------+------------------------+--------+-------------+------------+-------------+
19428|     19434|public    |halls          |halls_name_key          |       0|             |           0|            0|
19437|     19443|public    |seat_categories|seat_categories_name_key|       0|             |           0|            0|
19387|     19395|public    |clients        |clients_email_key       |       0|             |           0|            0|
19419|     19425|public    |movies         |movies_name_key         |       0|             |           0|            0|
19463|     19468|public    |seat_prices    |seat_prices_pkey        |       0|             |           0|            0|
*/