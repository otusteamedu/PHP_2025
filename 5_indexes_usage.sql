-- ============================================
-- 5 самых часто используемых индексов
-- ============================================
SELECT
    n.nspname AS schema_name,
    c.relname AS table_name,
    psui.indexrelname AS index_name,
    psui.idx_scan AS scan_count
FROM
    pg_stat_user_indexes psui
        JOIN
    pg_index i ON psui.indexrelid = i.indexrelid
        JOIN
    pg_class c ON i.indrelid = c.oid
        JOIN
    pg_namespace n ON n.oid = c.relnamespace
WHERE
    n.nspname NOT IN ('pg_catalog', 'information_schema')
ORDER BY
    psui.idx_scan DESC
    LIMIT 5;
-- schema_name|table_name|index_name |scan_count|
-- -----------+----------+-----------+----------+
-- public     |halls     |halls_pk   | 130390373|
-- public     |sessions  |sessions_pk| 120360557|
-- public     |seats     |seats_pk   | 120360339|
-- public     |tickets   |tickets_pk | 120360226|
-- public     |films     |films_pk   |  10057922|
-- ============================================
-- 5 самых редко используемых индексов
-- ============================================
SELECT
    n.nspname AS schema_name,
    c.relname AS table_name,
    psui.indexrelname AS index_name,
    psui.idx_scan AS scan_count
FROM
    pg_stat_user_indexes psui
        JOIN
    pg_index i ON psui.indexrelid = i.indexrelid
        JOIN
    pg_class c ON i.indrelid = c.oid
        JOIN
    pg_namespace n ON n.oid = c.relnamespace
WHERE
    n.nspname NOT IN ('pg_catalog', 'information_schema')
ORDER BY
    psui.idx_scan ASC
    LIMIT 5;
-- schema_name|table_name|index_name       |scan_count|
-- -----------+----------+-----------------+----------+
-- public     |sessions  |idx_hash_start_at|        11|
-- public     |sessions  |idx_start_at_2   |        16|
-- public     |sessions  |idx_id           |        17|
-- public     |orders    |idx_sale_at      |        30|
-- public     |tickets   |idx_session_id   |        67|
