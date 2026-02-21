-- 4. Поиск 3 самых прибыльных фильмов за неделю

EXPLAIN ANALYZE
SELECT
    m.name AS movie_name,
    sum(t.price) AS revenue
FROM
    cinema.ticket t
JOIN
    cinema."order" o
    ON t.order_id = o.id
JOIN
    cinema.session s
    ON t.session_id = s.id
JOIN
    cinema.movie m
    ON s.movie_id = m.id
WHERE
    o.created_at BETWEEN
        current_date - 7
        AND current_date - 1
GROUP BY
    m.id, m.name
ORDER BY
    revenue DESC
LIMIT 3;
