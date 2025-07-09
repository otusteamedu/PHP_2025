-- Добавление 10.000.000 тестовых данных
-- Заполнение таблицы "Тип места"
INSERT INTO "cinema".seat_types("ID", "TITLE")
SELECT
    gs.id,
    'type'||gs.id::TEXT
FROM generate_series(1, 4) AS gs(id);

-- Заполнение таблицы "Залы"
INSERT INTO "cinema".halls("ID", "NAME", "CAPACITY", "DESCRIPTION")
SELECT
    gs.id,
    'Зал №'||gs.id::TEXT,
    floor(random()*(450 - 400 + 1)) + 400,
    CASE
        WHEN random() > 0.5 THEN 'После ремонта. ' || substring(md5(random()::text), 1, 10)
        ELSE 'Без ремонта. ' || substring(md5(random()::text), 1, 15)
        END
FROM generate_series(1, 5) AS gs(id);

-- Заполнение таблицы "Места"
INSERT INTO "cinema".seats("ROW_NUMBER", "SEAT_NUMBER", "SEAT_TYPE_ID", "HALL_ID")
SELECT
    rs.id,
    ss.id,
    CASE
        WHEN rs.id <= 5 THEN 1
        WHEN rs.id <= 10 THEN 2
        WHEN rs.id <= 15 THEN 3
        ELSE 4
        END,
    hs.id
FROM generate_series(1, 20) AS rs(id)
    JOIN generate_series(1, 5) AS hs(id) ON true
    JOIN generate_series(1, 20) AS ss(id) ON true
ORDER BY 4, 1, 2;

-- Заполнение таблицы "Фильмы"
INSERT INTO "cinema".movies("ID", "TITLE", "DURATION", "GENRE", "RELEASE_DATE")
SELECT
    gs.id,
    'Фильм ' || gs.id,
    80 + (random() * 119 + 1),
    substring(md5(random()::text), 1, 10),
    date_trunc('day', timestamp '2025-01-01' + random() * (timestamp '2025-06-30' - timestamp '2025-01-01'))
FROM generate_series(1, 500) AS gs(id);

-- Заполнение таблицы "Посетители"
INSERT INTO "cinema".customers("ID", "NAME", "EMAIL", "PHONE", "REGISTER_DATA")
SELECT
    gs.id,
    substring(md5(random()::text), 1, 20),
    CASE
        WHEN random() > 0.5 THEN substring(md5(random()::text), 1, 8) || round((111 + random() * 888))::text || '@mail.ru'
        ELSE substring(md5(random()::text), 1, 8) || round((111 + random() * 777))::text || '@yandex.ru'
        END,
    round((88000000000 + random() * 99999999))::text,
    date_trunc('day', timestamp '2024-01-01' + random() * (timestamp '2024-12-31' - timestamp '2024-01-01'))
FROM generate_series(1, 5000000) AS gs(id);

-- Заполнение таблицы "Показы"
INSERT INTO "cinema".screenings("HALL_ID", "SCREENING_START", "SCREENING_END", "BASE_PRICE", "MOVIE_ID")
SELECT
    hs.id,
    timestamp '2024-03-01 09:00' + (3 * (ss.id - 1) + 9 * floor((ss.id - 1) / 5))::integer * interval '1 hour',
    timestamp '2024-03-01 09:00' + (3 * (ss.id - 1) + 9 * floor((ss.id - 1) / 5))::integer * interval '1 hour' + round(80 + random() * 100)::integer * interval '1 minute',
    round((1000 + random() * 1000))::integer,
    round(random() * 499 + 1)::integer
FROM generate_series(1, 5) AS hs(id)
    JOIN generate_series(1, 10000) AS ss(id) ON true;

-- Заполнение таблицы "Ценообразование"
INSERT INTO "cinema".pricing_rules("SCREENING_ID", "SEAT_TYPE_ID", "MODIFIER")
SELECT
    ss.id,
    ts.id,
    (1 + random())::numeric(4,2)
FROM generate_series(1, 50000) AS ss(id)
         JOIN generate_series(1, 4) AS ts(id) ON true;

-- Генерация тестовых записей о продажах билетов
WITH cte AS (
    SELECT
        ss.id AS screening_id,
        ts.id AS tid,
        round((random() * 4999999 + 1))::integer AS customer_id,
        timestamp '2024-01-01 09:00' + (random() * 2200)::integer * interval '1 day' AS  purchase_date
    FROM generate_series(1, 50000) AS ss(id)
             JOIN generate_series(1, 200) AS ts(id) ON true
    ORDER BY 2
),
     cte2 AS (
         SELECT
             customer_id,
             screening_id,
             CASE
                 WHEN random() < 0.5 THEN cte.tid + (s."HALL_ID" - 1) * 400
                 ELSE cte.tid + 200 + (s."HALL_ID" - 1) * 400
                 END AS seat_id,
             purchase_date
         FROM cte
             JOIN "cinema".screenings s ON s."ID" = cte.screening_id
     )
INSERT INTO "cinema".tickets("CUSTOMER_ID", "SCREENING_ID", "SEAT_ID", "PRICE", "PURCHASE_DATE")
SELECT
    cte2.customer_id,
    cte2.screening_id,
    cte2.seat_id,
    (s."BASE_PRICE" * pr."MODIFIER")::smallint,
    cte2.purchase_date
FROM cte2
         LEFT JOIN "cinema".screenings s ON s."ID" = cte2.screening_id
         LEFT JOIN "cinema".seats se ON se."ID" = cte2.seat_id
         LEFT JOIN "cinema".pricing_rules pr ON pr."SCREENING_ID" = cte2.screening_id AND pr."SEAT_TYPE_ID" = se."SEAT_TYPE_ID"
ORDER BY 2, 3;
