-- Запросы для нагрузочного тестирования

-- 1. Выбор всех фильмов на сегодня
SELECT DISTINCT m.title
FROM MOVIE m
JOIN SESSION s ON m.id = s.movie_id
WHERE DATE(s.start_time) = CURRENT_DATE
ORDER BY m.title;

-- 2. Подсчёт проданных билетов за неделю
SELECT COUNT(*)
FROM TICKET t
JOIN SESSION s ON t.session_id = s.id
WHERE s.start_time >= CURRENT_DATE - INTERVAL '7 days'
AND s.start_time <= CURRENT_DATE + INTERVAL '1 day';

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
SELECT
    m.title,
    s.start_time,
    s.end_time,
    h.name,
    s.base_price
FROM SESSION s
JOIN MOVIE m ON s.movie_id = m.id
JOIN HALL h ON s.hall_id = h.id
WHERE DATE(s.start_time) = CURRENT_DATE
ORDER BY s.start_time, h.name;

-- 4. Поиск 3 самых прибыльных фильмов за неделю
SELECT
    m.id,
    m.title,
    SUM(t.price) AS total_sum
FROM TICKET t
JOIN SESSION s ON t.session_id = s.id
JOIN MOVIE m ON s.movie_id = m.id
WHERE s.start_time >= CURRENT_DATE - INTERVAL '7 days'
AND s.start_time <= CURRENT_DATE + INTERVAL '1 day'
GROUP BY m.id, m.title
ORDER BY total_sum DESC
LIMIT 3;

-- 5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
WITH occupied_seats AS (
    SELECT hall_seat_id
    FROM TICKET
    WHERE session_id = 1  -- ID сеанса
)
SELECT
    hs.row_number AS "Ряд",
    hs.seat_number AS "Место",
    CASE
        WHEN os.hall_seat_id IS NOT NULL THEN 'Занято'
        ELSE 'Свободно'
    END AS "Статус"
FROM HALL_SEAT hs
JOIN HALL h ON hs.hall_layout_id = h.hall_layout_id
JOIN SESSION s ON s.hall_id = h.id
LEFT JOIN occupied_seats os ON os.hall_seat_id = hs.id
WHERE s.id = 1  -- ID сеанса
ORDER BY hs.row_number, hs.seat_number;

-- 6. Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс
SELECT
    MIN(t.price) AS min_price,
    MAX(t.price) AS max_price
FROM TICKET t
WHERE t.session_id = 1;  -- ID сеанса
