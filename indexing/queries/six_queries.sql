-- выбор всех фильмов на сегодня
SELECT DISTINCT m.movie_id, m.title, m.duration, m.age_rating
FROM movie m
         JOIN screening s ON m.movie_id = s.movie_id
WHERE s.show_date = CURRENT_DATE;

-- подсчёт проданных билетов за неделю
SELECT COUNT(*) AS tickets_sold_last_week
FROM ticket
WHERE purchase_time >= CURRENT_DATE - INTERVAL '7 days';

-- формирование афиши (фильмы, которые показывают сегодня)
SELECT DISTINCT m.title, s.show_time, s.hall_id, s.base_price
FROM movie m
         JOIN screening s ON m.movie_id = s.movie_id
WHERE s.show_date = CURRENT_DATE
ORDER BY s.show_time;

-- поиск 3 самых прибыльных фильмов за неделю
SELECT m.movie_id, m.title, SUM(t.price) AS total_revenue
FROM ticket t
         JOIN screening s ON t.screening_id = s.screening_id
         JOIN movie m ON s.movie_id = m.movie_id
WHERE t.purchase_time >= CURRENT_DATE - INTERVAL '7 days'
GROUP BY m.movie_id, m.title
ORDER BY total_revenue DESC
    LIMIT 3;

-- схема зала - свободные и занятые места на конкретный сеанс
SELECT
    s.row_number,
    s.seat_number,
    st.name AS seat_type,
    CASE WHEN t.ticket_id IS NOT NULL THEN 'Занято' ELSE 'Свободно' END AS status,
    sc.base_price * st.price_modifier AS price
FROM seat s
         JOIN seat_type st ON s.seat_type_id = st.seat_type_id
         JOIN hall h ON s.hall_id = h.hall_id
         JOIN screening sc ON sc.hall_id = h.hall_id
         LEFT JOIN ticket t ON t.screening_id = sc.screening_id AND t.seat_id = s.seat_id
WHERE sc.screening_id = 1
ORDER BY s.row_number, s.seat_number;

-- диапазон цен за билет на конкретный сеанс
SELECT
    MIN(sc.base_price * st.price_modifier) AS min_price,
    MAX(sc.base_price * st.price_modifier) AS max_price,
    COUNT(DISTINCT st.seat_type_id) AS seat_types_count
FROM screening sc
         JOIN hall h ON sc.hall_id = h.hall_id
         JOIN seat s ON s.hall_id = h.hall_id
         JOIN seat_type st ON s.seat_type_id = st.seat_type_id
WHERE sc.screening_id = 1;