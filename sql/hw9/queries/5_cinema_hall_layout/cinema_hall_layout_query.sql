-- 5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

EXPLAIN ANALYZE
SELECT
    s.id AS session_id,
    seat.hall_id,
    seat.row_number || ' ряд, ' || seat.seat_number || ' место' AS hall_seat,
    CASE WHEN (t.id IS NULL) THEN 'Свободно' ELSE 'Занято' END AS booking
FROM
    cinema.session s
JOIN
    cinema.seat
    ON s.hall_id = seat.hall_id
LEFT JOIN
    cinema.ticket t
    ON s.id = t.session_id AND seat.id = t.seat_id
WHERE s.id = (
    SELECT s.id
    FROM cinema.session s
    WHERE s.start_time::date = current_date
    ORDER BY random()
    LIMIT 1
)
ORDER BY
    seat.row_number,
    seat.seat_number;
