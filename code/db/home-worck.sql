-- Вызов функций для заполнения базы данных
SELECT populate_schedule(15);
SELECT populate_tickets_for_sessions();


-- Выбор всех фильмов на сегодня
EXPLAIN SELECT f.title, s.date FROM films AS f
    INNER JOIN sessions as s on f.id = s.film_id
where CAST(s.date AS DATE) = CURRENT_DATE;

-- Подсчёт проданных билетов за неделю
EXPLAIN  SELECT count(tickets.id) FROM sessions
    INNER JOIN tickets on sessions.id = tickets.session_id
WHERE CAST(sessions.date AS DATE) BETWEEN  CURRENT_DATE - INTERVAL '6days' AND CURRENT_DATE;

-- Фильмы, которые показывают сегодня
EXPLAIN SELECT films.title,sessions.date::TIME  FROM films
     INNER JOIN sessions on films.id = sessions.film_id
WHERE CAST(sessions.date AS DATE) = CURRENT_DATE
ORDER BY  sessions.date;

-- Поиск 3 самых прибыльных фильмов за сегодня (с учетом всех сеансов)
EXPLAIN SELECT
    f.title,
    SUM(s.price) AS total_earn
FROM films AS f
JOIN sessions s ON f.id = s.film_id
JOIN tickets t ON s.id = t.session_id
WHERE CAST(s.date AS DATE) = CURRENT_DATE
GROUP BY f.id, f.title
ORDER BY total_earn DESC
LIMIT 3;

-- Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
EXPLAIN  WITH
    target_session AS (SELECT 14 AS session_id),

    hall_dimensions AS (
        SELECT
            h.rows AS total_rows,
            h.seats_per_row AS seats_per_row
        FROM sessions s
                 JOIN session_hall sh ON s.id = sh.session_id
                 JOIN halls h ON sh.hall_id = h.id
        WHERE s.id = (SELECT session_id FROM target_session)
    ),

    all_seats AS (
        SELECT
            row_num,
            seat_num
        FROM
            hall_dimensions,
            generate_series(1, hall_dimensions.total_rows) AS row_num,
            generate_series(1, hall_dimensions.seats_per_row) AS seat_num
    ),

    taken_seats AS (
        SELECT
            t.row_number,
            t.seat_number
        FROM tickets t
        WHERE t.session_id = (SELECT session_id FROM target_session)
    )

SELECT
    a_s.row_num AS "Ряд",
    a_s.seat_num AS "Место",
    CASE
        WHEN ts.row_number IS NOT NULL THEN 'Занято'
        ELSE 'Свободно'
        END AS "Статус"
FROM all_seats AS a_s
         LEFT JOIN taken_seats AS ts ON a_s.row_num = ts.row_number AND a_s.seat_num = ts.seat_number
ORDER BY "Ряд", "Место";