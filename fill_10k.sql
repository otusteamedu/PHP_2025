-- Заполнение залов
INSERT INTO hall (name)
SELECT 'Зал №' || i || ' - ' ||
       CASE (i % 5)
           WHEN 0 THEN 'IMAX'
           WHEN 1 THEN 'VIP'
           WHEN 2 THEN 'Стандарт'
           WHEN 3 THEN '3D'
           ELSE 'Эконом'
           END
FROM generate_series(1, 50) AS i;

-- Заполнение фильмов
INSERT INTO movie (title)
SELECT 'Фильм ' || LPAD(i::text, 4, '0') || ' - ' ||
       substring(md5(i::text), 1, 25)
FROM generate_series(1, 500) AS i;

-- Заполнение типов мест
INSERT INTO seat_type (type, price)
VALUES
    ('Эконом', 200.00),
    ('Стандарт', 350.00),
    ('Комфорт', 450.00),
    ('VIP', 600.00),
    ('Люкс', 800.00);

-- Заполнение мест
INSERT INTO seat (hall_id, row_num, seat_num, seat_type_id)
SELECT
    h.id,
    r.row_num,
    s.seat_num,
    CASE
        WHEN r.row_num <= 2 THEN 1
        WHEN r.row_num <= 4 THEN 2
        WHEN r.row_num <= 6 THEN 3
        WHEN r.row_num = 7 THEN 4
        ELSE 5
        END
FROM hall h
        CROSS JOIN generate_series(1, 8) AS r(row_num)
        CROSS JOIN generate_series(1, 5) AS s(seat_num);

-- Заполнение сеансов
INSERT INTO showtime (hall_id, movie_id, time)
SELECT
    ((i - 1) % 50) + 1,
    ((i - 1) % 500) + 1,
    CURRENT_DATE - INTERVAL '30 days' + ((i - 1) * INTERVAL '34 minutes')
FROM generate_series(1, 2500) AS i;

-- Заполнение покупателей
INSERT INTO customer (first_name, last_name, email, phone)
SELECT
    'Имя_' || LPAD(i::text, 4, '0'),
    'Фамилия_' || LPAD(i::text, 4, '0'),
    'user' || i || '@cinema' || ((i % 10) + 1) || '.ru',
    '+79' || LPAD((i % 999999999)::text, 9, '0')
FROM generate_series(1, 2000) AS i;

-- Заполнение заказов
INSERT INTO "order" (customer_id, showtime_id, seat_id, order_time, price)
SELECT
    ((i - 1) % 2000) + 1 AS customer_id,
    ((i - 1) / 40) + 1 AS showtime_id,
    (((i - 1) % 40) * 50 + (((i - 1) / 40) % 50)) + 1 AS seat_id,
    CURRENT_TIMESTAMP - (random() * INTERVAL '30 days') AS order_time,
    (SELECT st.price
     FROM seat s
              JOIN seat_type st ON s.seat_type_id = st.id
     WHERE s.id = (((i - 1) % 40) * 50 + (((i - 1) / 40) % 50)) + 1
     LIMIT 1) AS price
FROM generate_series(1, 2945) AS i
WHERE ((i - 1) / 40) + 1 <= 2500;

ANALYZE;
