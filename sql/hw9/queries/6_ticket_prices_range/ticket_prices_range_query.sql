-- 6. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс

EXPLAIN ANALYZE
SELECT
    s.id AS session_id,
    min(sp.price) || ' - ' || max(sp.price) AS price_range
FROM
    cinema.session s
JOIN
    cinema.session_price sp
    ON s.id = sp.session_id
WHERE s.id = (
    SELECT s.id
    FROM cinema.session s
    WHERE s.start_time::date = current_date
    ORDER BY random()
    LIMIT 1
)
GROUP BY
    s.id;
