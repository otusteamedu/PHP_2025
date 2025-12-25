--  1. Выбор всех фильмов на сегодня
SELECT m."TITLE", m."DURATION", m."RELEASE_DATE"
FROM "cinema".movies m
    LEFT JOIN "cinema".screenings s ON s."MOVIE_ID" = m."ID"
WHERE DATE(s."SCREENING_START") = CURRENT_DATE
GROUP BY m."TITLE", m."DURATION", m."RELEASE_DATE"
ORDER BY m."TITLE";

-- 2. Подсчёт проданных билетов за неделю
SELECT COUNT(DISTINCT t."ID") AS tickets_week_count
FROM "cinema".tickets t
    LEFT JOIN "cinema".screenings s ON s."ID" = t."SCREENING_ID"
WHERE DATE(s."SCREENING_START") > CURRENT_DATE - INTERVAL '10 days' AND DATE(s."SCREENING_START") <= CURRENT_DATE;

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
SELECT m."TITLE", m."DURATION", m."RELEASE_DATE", to_char(s."SCREENING_START", 'HH24:MI') AS start_time, h."NAME" AS hall
FROM "cinema".movies m
         LEFT JOIN "cinema".screenings s ON s."MOVIE_ID" = m."ID"
         LEFT JOIN "cinema".halls h ON h."ID" = s."HALL_ID"
WHERE DATE(s."SCREENING_START") = CURRENT_DATE
ORDER BY start_time, hall;

-- 4. Поиск 3 самых прибыльных фильмов за неделю
SELECT m."TITLE", SUM(t."PRICE") AS total
FROM "cinema".movies m
         LEFT JOIN "cinema".screenings s ON s."MOVIE_ID" = m."ID"
         LEFT JOIN "cinema".tickets t ON t."SCREENING_ID" = s."ID"
WHERE DATE(s."SCREENING_START") > CURRENT_DATE - INTERVAL '7 days' AND DATE(s."SCREENING_START") <= CURRENT_DATE
GROUP BY m."TITLE"
ORDER BY total DESC
LIMIT 3;

--  5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
WITH cte AS
(
    SELECT se."ROW_NUMBER", se."SEAT_NUMBER", ti."ID" AS ticket_id
    FROM "cinema".screenings sc
             LEFT JOIN "cinema".halls ha ON ha."ID" = sc."HALL_ID"
             LEFT JOIN "cinema".seats se ON se."HALL_ID" = ha."ID"
             LEFT JOIN "cinema".tickets ti ON ti."SCREENING_ID" = sc."ID" AND ti."SEAT_ID" = se."ID"
    WHERE sc."ID" = 1
)
SELECT
    "ROW_NUMBER",
    STRING_AGG((CASE WHEN ticket_id IS NOT NULL THEN '1 ' ELSE '0 ' END)::TEXT, '' ORDER BY "SEAT_NUMBER") AS row_seats
FROM cte
GROUP BY "ROW_NUMBER"
ORDER BY "ROW_NUMBER";

-- 6. Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс
SELECT MIN(t."PRICE") AS min_price, MAX(t."PRICE") AS max_price
FROM "cinema".screenings s
         LEFT JOIN "cinema".tickets t ON t."SCREENING_ID" = s."ID"
WHERE s."ID" = 1;
