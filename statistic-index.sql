-- Самые часто используемые индексы
SELECT
    indexrelname AS index_name,
    idx_scan AS usage_count
FROM
    pg_stat_user_indexes
WHERE
        schemaname = 'public'
ORDER BY
    idx_scan DESC
    LIMIT 5;

+------------+-----------+
|index_name  |usage_count|
+------------+-----------+
|films_pkey  |10608296   |
|session_pkey|10444523   |
|rooms_pkey  |10000002   |
|clients_pkey|10000001   |
|cinema_pkey |10000000   |
+------------+-----------+


-- Самые редко используемые индексы
SELECT
    indexrelname AS index_name,
    idx_scan AS usage_count
FROM
    pg_stat_user_indexes
WHERE
        schemaname = 'public'
ORDER BY
    idx_scan ASC
    LIMIT 5;

+---------------------+-----------+
|index_name           |usage_count|
+---------------------+-----------+
|idx-sessions-film_id |0          |
|tickets_pkey         |0          |
|idx-orders-created_at|0          |
|idx-orders-status    |0          |
|idx-sessions-room_id |0          |
+---------------------+-----------+
