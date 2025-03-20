-- Oтсортированный список самых больших по размеру объектов БД (таблицы, включая индексы, сами индексы)
SELECT
    TABLE_NAME,
    pg_size_pretty(table_size) AS table_size,
    pg_size_pretty(indexes_size) AS indexes_size,
    pg_size_pretty(total_size) AS total_size
FROM (
         SELECT
             TABLE_NAME,
             pg_table_size(TABLE_NAME) AS table_size,
             pg_indexes_size(TABLE_NAME) AS indexes_size,
             pg_total_relation_size(TABLE_NAME) AS total_size
         FROM (
                  SELECT ('"' || table_schema || '"."' || TABLE_NAME || '"') AS TABLE_NAME
                  FROM information_schema.tables
              ) AS all_tables
         ORDER BY total_size DESC LIMIT 15
     ) AS pretty_sizes;


-- отсортированные списки (по 5 значений) самых часто и редко используемых индексов ORDER BY idx_scan (ASC/DESC)
SELECT
    t.relname AS имя_таблицы,
    i.relname AS имя_индекса,
    s.idx_scan,
    s.idx_tup_read,
    s.idx_tup_fetch
FROM
    pg_stat_user_indexes s
        JOIN
    pg_index x ON s.indexrelid = x.indexrelid
        JOIN
    pg_class t ON x.indrelid = t.oid
        JOIN
    pg_class i ON s.indexrelid = i.oid ORDER BY idx_scan ASC LIMIT 5;

