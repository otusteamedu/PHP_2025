-- Добавление индексов для оптимизации

CREATE INDEX idx_ticket_seat ON tickets (ticket_seat);

CREATE INDEX idx_ticket_order ON tickets (ticket_order);

CREATE INDEX idx_ticket_movie_session ON tickets (ticket_movie_session);

CREATE INDEX idx_ms_movie ON movie_sessions (ms_movie);

CREATE INDEX idx_mr_type ON movie_rooms (mr_type);

CREATE INDEX idx_brin_ms_start_time ON movie_sessions USING BRIN (ms_start_time);

CREATE INDEX idx_brin_order_payment_time ON orders USING BRIN (order_payment_time);

-- Переписана функция
CREATE OR REPLACE FUNCTION calculate_time_coefficient(start_time TIMESTAMP)
RETURNS DECIMAL(5, 2) AS $$
DECLARE
    coefficient DECIMAL(5, 2);
BEGIN
    -- Используем CASE для определения коэффициента
    coefficient := CASE 
        WHEN EXTRACT(HOUR FROM start_time) = 10 AND EXTRACT(MINUTE FROM start_time) = 0 THEN 0.6
        WHEN EXTRACT(HOUR FROM start_time) = 18 AND EXTRACT(MINUTE FROM start_time) >= 0 THEN 1.15
        WHEN EXTRACT(HOUR FROM start_time) >= 11 AND EXTRACT(HOUR FROM start_time) < 18 THEN 1.0
        ELSE 1.0 -- Значение по умолчанию
    END;

    RETURN coefficient;
END;
$$ LANGUAGE plpgsql;

-- 1. Выбор всех фильмов на сегодня
-- Оптимизированный запрос с использованием диапазона
SELECT movie_title
FROM movies
    JOIN movie_sessions ON movie_sessions.ms_movie = movies.movie_id
WHERE
    ms_start_time >= CURRENT_DATE
    AND ms_start_time < CURRENT_DATE + INTERVAL '1 day'
GROUP BY
    movie_title;

-- 2. Подсчёт проданных билетов за неделю
-- НЕ выводим лишние данные, а считаем только количество проданных билетов
SELECT COUNT(*) AS "Tickets count", SUM(
        ms_time_coefficient * seat_coefficient * mrt_price
    ) AS "Week profit"
FROM
    seats
    JOIN tickets_partitioned ON ticket_seat = seat_id
    JOIN orders ON order_id = ticket_order
    JOIN movie_sessions ON ms_id = ticket_movie_session
    JOIN movie_rooms ON mr_id = ms_movie_room
    JOIN movie_room_types ON mrt_id = mr_type
WHERE
    order_payment_time >= NOW() - INTERVAL '7 days'
    AND order_payment_time < CURRENT_DATE
    AND order_id IS NOT NULL;

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
-- Оптимизированный запрос с использованием диапазона
-- С учетом времени начала сеанса
SELECT
    movie_title,
    movie_duration,
    ms_start_time
FROM movies
    JOIN movie_sessions ON movie_sessions.ms_movie = movies.movie_id
WHERE
    ms_start_time >= NOW()
    AND ms_start_time < CURRENT_DATE + INTERVAL '1 day';

-- 4. Поиск 3 самых прибыльных фильмов за неделю
SELECT movie_title, SUM(
        ms_time_coefficient * seat_coefficient * mrt_price
    ) AS "Week profit"
FROM
    tickets_partitioned
    LEFT JOIN seats ON seat_id = ticket_seat
    LEFT JOIN movie_sessions ON ms_id = ticket_movie_session
    LEFT JOIN movie_rooms ON movie_rooms.mr_id = ms_movie_room
    LEFT JOIN movie_room_types ON movie_room_types.mrt_id = movie_rooms.mr_type
    LEFT JOIN movies ON movie_id = ms_movie
    LEFT JOIN (
        SELECT order_id
        FROM orders
        WHERE
            order_payment_time >= CURRENT_DATE - INTERVAL '7 days'
            AND order_payment_time < CURRENT_DATE
    ) t ON t.order_id = ticket_order
WHERE
    order_id IS NOT NULL
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
        ms_time_coefficient * seat_coefficient * mrt_price
    ) AS "Min ticket price", MAX(
        ms_time_coefficient * seat_coefficient * mrt_price
    ) AS "Max ticket price"
FROM
    seats
    JOIN tickets_partitioned ON ticket_seat = seat_id
    JOIN orders ON order_id = ticket_order
    JOIN movie_sessions ON ms_id = ticket_movie_session
    JOIN movie_rooms ON mr_id = ms_movie_room
    JOIN movie_room_types ON mrt_id = mr_type
WHERE
    ms_id = 10018272;