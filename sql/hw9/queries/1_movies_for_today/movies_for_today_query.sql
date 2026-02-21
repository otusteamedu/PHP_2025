-- 1. Выбор всех фильмов на сегодня

EXPLAIN ANALYZE
SELECT DISTINCT
    m.name AS movie_name
FROM
    cinema.session s
JOIN
    cinema.movie m
    ON s.movie_id = m.id
WHERE
    s.start_time::date = current_date;
