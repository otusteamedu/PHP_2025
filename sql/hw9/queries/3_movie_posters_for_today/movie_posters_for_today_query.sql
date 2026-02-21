-- 3. Формирование афиши (фильмы, которые показывают сегодня)

EXPLAIN ANALYZE
SELECT
    m.name AS movie_name,
    m.description AS movie_description,
    m.duration / 60 || ' ч ' || m.duration % 60 || ' мин' AS movie_duration,
    m.rating AS movie_rating,
    string_agg(to_char(s.start_time, 'HH24:MI'), ', ' ORDER BY s.start_time) AS show_time
FROM
    cinema.session s
JOIN
    cinema.movie m
    ON s.movie_id = m.id
WHERE
    s.start_time::date = current_date
GROUP BY
    m.name,
    m.description,
    m.duration,
    m.rating;
