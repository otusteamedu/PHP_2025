-- 1. Выбор всех фильмов на сегодня
SELECT movie_title
FROM movies
    JOIN movie_sessions ON movie_sessions.ms_movie = movies.movie_id
WHERE
    DATE(movie_sessions.ms_start_time) = CURRENT_DATE
GROUP BY
    movie_title;

-- 2. Подсчёт проданных билетов за неделю
SELECT COUNT(*) AS "Tickets count", SUM(
        calculate_time_coefficient (ms_start_time) * seat_coefficient * mrt_price
    ) AS "Week profit"
FROM
    seats
    JOIN tickets ON tickets.ticket_seat = seats.seat_id
    JOIN orders ON orders.order_id = tickets.ticket_order
    JOIN movie_sessions ON movie_sessions.ms_id = tickets.ticket_movie_session
    JOIN movie_rooms ON movie_rooms.mr_id = movie_sessions.ms_movie_room
    JOIN movie_room_types ON movie_room_types.mrt_id = movie_rooms.mr_type
WHERE
    order_payment_time >= NOW() - INTERVAL '7 days';

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
SELECT
    movie_title,
    movie_duration,
    ms_start_time
FROM movies
    JOIN movie_sessions ON movie_sessions.ms_movie = movies.movie_id
WHERE
    DATE(movie_sessions.ms_start_time) = CURRENT_DATE;

-- 4. Поиск 3 самых прибыльных фильмов за неделю
SELECT movie_title, SUM(
        calculate_time_coefficient (ms_start_time) * seat_coefficient * mrt_price
    ) AS "Week profit"
FROM
    seats
    JOIN tickets ON tickets.ticket_seat = seats.seat_id
    JOIN orders ON orders.order_id = tickets.ticket_order
    JOIN movie_sessions ON movie_sessions.ms_id = tickets.ticket_movie_session
    JOIN movie_rooms ON movie_rooms.mr_id = movie_sessions.ms_movie_room
    JOIN movie_room_types ON movie_room_types.mrt_id = movie_rooms.mr_type
    JOIN movies ON movies.movie_id = movie_sessions.ms_movie
WHERE
    order_payment_time >= NOW() - INTERVAL '7 days'
GROUP BY
    movie_title
ORDER BY "Week profit" DESC
LIMIT 3;

-- 5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
SELECT s.seat_id, s.seat_row, s.seat_cell, COALESCE(t.seat_status, 'свободно') AS "Статус"
FROM seats s
    LEFT JOIN (
        SELECT ticket_seat, 'занято' AS seat_status
        FROM tickets
            JOIN movie_sessions ON movie_sessions.ms_id = tickets.ticket_movie_session
        WHERE
            ms_id = 10018272
    ) t ON s.seat_id = t.ticket_seat;

-- 6. Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс
SELECT MIN(
        calculate_time_coefficient (ms_start_time) * seat_coefficient * mrt_price
    ) AS "Min ticket price", MAX(
        calculate_time_coefficient (ms_start_time) * seat_coefficient * mrt_price
    ) AS "Max ticket price"
FROM
    seats
    JOIN tickets ON tickets.ticket_seat = seats.seat_id
    JOIN orders ON orders.order_id = tickets.ticket_order
    JOIN movie_sessions ON movie_sessions.ms_id = tickets.ticket_movie_session
    JOIN movie_rooms ON movie_rooms.mr_id = movie_sessions.ms_movie_room
    JOIN movie_room_types ON movie_room_types.mrt_id = movie_rooms.mr_type
WHERE
    ms_id = 10018272;

