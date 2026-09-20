-- ДЗ-9 «Индексирование данных»
-- Аналитические скрипты: размеры объектов БД и статистика использования индексов

\echo '=== Параметры сервера (администрирование) ==='
SELECT name, setting, unit, source, pending_restart
FROM pg_settings
WHERE name IN (
    'shared_buffers', 'effective_cache_size', 'work_mem',
    'maintenance_work_mem', 'random_page_cost', 'effective_io_concurrency',
    'checkpoint_completion_target', 'max_wal_size', 'wal_buffers', 'jit'
)
ORDER BY name;

\echo '=== ТО-15 самых больших объектов БД (таблицы + индексы) ==='
SELECT c.relname                         AS object_name,
       CASE c.relkind
           WHEN 'r' THEN 'table'
           WHEN 'i' THEN 'index'
           WHEN 'p' THEN 'partitioned table'
           WHEN 'I' THEN 'partitioned index'
           END                           AS kind,
       pg_size_pretty(pg_total_relation_size(c.oid)) AS total_size,
       pg_size_pretty(pg_relation_size(c.oid))       AS own_size,
       pg_total_relation_size(c.oid)     AS bytes
FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
WHERE n.nspname = 'public'
  AND c.relkind IN ('r', 'i', 'p', 'I')
  AND pg_total_relation_size(c.oid) > 0
ORDER BY bytes DESC
LIMIT 15;

\echo '=== ТО-15 таблиц по размеру вместе с индексами ==='
SELECT c.relname                                    AS table_name,
       pg_size_pretty(pg_total_relation_size(c.oid)) AS total_size,
       pg_size_pretty(pg_indexes_size(c.oid))        AS indexes_size,
       pg_size_pretty(pg_relation_size(c.oid))       AS table_size
FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
WHERE n.nspname = 'public'
  AND c.relkind IN ('r', 'p')
  AND pg_total_relation_size(c.oid) > 0
ORDER BY pg_total_relation_size(c.oid) DESC
LIMIT 15;

\echo '=== Секции tickets (родитель виртуальный, хранение в детях) ==='
SELECT c.relname                                    AS partition,
       pg_size_pretty(pg_total_relation_size(c.oid)) AS total_size,
       n.n_live_tup                                 AS rows
FROM pg_inherits i
         JOIN pg_class c ON c.oid = i.inhrelid
         JOIN pg_stat_user_tables n ON n.relid = c.oid
WHERE i.inhparent = 'tickets'::regclass
ORDER BY pg_total_relation_size(c.oid) DESC;

\echo '=== ТО-5 самых часто используемых индексов ==='
SELECT s.indexrelid::regclass        AS index_name,
       s.idx_scan                    AS scans,
       pg_size_pretty(pg_relation_size(s.indexrelid)) AS size
FROM pg_stat_user_indexes s
ORDER BY s.idx_scan DESC
LIMIT 5;

\echo '=== ТО-5 самых редко используемых индексов (0 сканов = никогда) ==='
SELECT s.indexrelid::regclass        AS index_name,
       s.idx_scan                    AS scans,
       pg_size_pretty(pg_relation_size(s.indexrelid)) AS size
FROM pg_stat_user_indexes s
ORDER BY s.idx_scan ASC, pg_relation_size(s.indexrelid) DESC
LIMIT 5;
