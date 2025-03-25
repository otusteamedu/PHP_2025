-- r - таблицы, i - индексы
SELECT c.relname                                     AS object_name,
       pg_size_pretty(pg_total_relation_size(c.oid)) AS total_size
FROM pg_class c
         JOIN
     pg_namespace n ON n.oid = c.relnamespace
WHERE c.relkind IN ('r', 'i')
ORDER BY pg_total_relation_size(c.oid) DESC LIMIT 15;
-- object_name      |total_size|
-- -----------------+----------+
-- tickets          |13 GB     |
-- seats            |10 GB     |
-- orders           |7933 MB   |
-- idx_session_id   |3864 MB   |
-- tickets_pk       |2571 MB   |
-- seats_pk         |2571 MB   |
-- sessions         |1971 MB   |
-- idx_sale_at      |1960 MB   |
-- films            |1045 MB   |
-- halls            |712 MB    |
-- idx_hash_start_at|445 MB    |
-- idx_film_id      |256 MB    |
-- idx_hash_id      |256 MB    |
-- idx_id           |256 MB    |
-- films_pk         |214 MB    |
