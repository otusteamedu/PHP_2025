-- region Тестовое наполнение

INSERT INTO users (email)
VALUES ('user1@example.com'),
       ('user2@example.com'),
       ('user3@example.com'),
       ('user4@example.com'),
       ('user5@example.com'),
       ('user6@example.com'),
       ('user7@example.com'),
       ('user8@example.com'),
       ('user9@example.com'),
       ('user10@example.com');

INSERT INTO movies (title, description, release_date, duration_minutes)
VALUES ('Dune: Part Two', 'Продолжение эпического научно-фантастического фильма.', '2024-03-01', 165),
       ('Oppenheimer', 'История создания первой атомной бомбы.', '2023-07-21', 180),
       ('Avatar: The Way of Water', 'Продолжение фильма "Аватар" с новыми приключениями на Пандоре.', '2022-12-16',
        192),
       ('John Wick 4', 'Четвертая часть саги о наемном убийце Джоне Уике.', '2023-03-24', 169),
       ('The Batman', 'Новый взгляд на героя Готэма в исполнении Роберта Паттинсона.', '2022-03-04', 176);


INSERT INTO halls (title, scheme)
VALUES ('Зал 1', '{"rows": 4, "seats_per_row": 12}'),
       ('Зал 2', '{"rows": 4, "seats_per_row": 12}'),
       ('Зал 3', '{"rows": 4, "seats_per_row": 12}');

INSERT INTO seats (hall_id, row_number, seat_number, seat_type)
VALUES
-- Зал 1
(1, 1, 1, 'regular'),
(1, 1, 2, 'regular'),
(1, 1, 3, 'regular'),
(1, 1, 4, 'regular'),
(1, 1, 5, 'regular'),
(1, 1, 6, 'regular'),
(1, 1, 7, 'regular'),
(1, 1, 8, 'regular'),
(1, 1, 9, 'regular'),
(1, 1, 10, 'regular'),
(1, 1, 11, 'regular'),
(1, 1, 12, 'regular'),
(1, 2, 1, 'regular'),
(1, 2, 2, 'regular'),
(1, 2, 3, 'regular'),
(1, 2, 4, 'regular'),
(1, 2, 5, 'regular'),
(1, 2, 6, 'regular'),
(1, 2, 7, 'regular'),
(1, 2, 8, 'regular'),
(1, 2, 9, 'regular'),
(1, 2, 10, 'regular'),
(1, 2, 11, 'regular'),
(1, 2, 12, 'regular'),
(1, 3, 1, 'vip'),
(1, 3, 2, 'vip'),
(1, 3, 3, 'vip'),
(1, 3, 4, 'vip'),
(1, 3, 5, 'vip'),
(1, 3, 6, 'vip'),
(1, 3, 7, 'vip'),
(1, 3, 8, 'vip'),
(1, 3, 9, 'vip'),
(1, 3, 10, 'vip'),
(1, 3, 11, 'vip'),
(1, 3, 12, 'vip'),
(1, 4, 1, 'vip'),
(1, 4, 2, 'vip'),
(1, 4, 3, 'vip'),
(1, 4, 4, 'vip'),
(1, 4, 5, 'vip'),
(1, 4, 6, 'vip'),
(1, 4, 7, 'vip'),
(1, 4, 8, 'vip'),
(1, 4, 9, 'vip'),
(1, 4, 10, 'vip'),
(1, 4, 11, 'vip'),
(1, 4, 12, 'vip'),

-- Зал 2 (аналогично)
(2, 1, 1, 'regular'),
(2, 1, 2, 'regular'),
(2, 1, 3, 'regular'),
(2, 1, 4, 'regular'),
(2, 1, 5, 'regular'),
(2, 1, 6, 'regular'),
(2, 1, 7, 'regular'),
(2, 1, 8, 'regular'),
(2, 1, 9, 'regular'),
(2, 1, 10, 'regular'),
(2, 1, 11, 'regular'),
(2, 1, 12, 'regular'),
(2, 2, 1, 'regular'),
(2, 2, 2, 'regular'),
(2, 2, 3, 'regular'),
(2, 2, 4, 'regular'),
(2, 2, 5, 'regular'),
(2, 2, 6, 'regular'),
(2, 2, 7, 'regular'),
(2, 2, 8, 'regular'),
(2, 2, 9, 'regular'),
(2, 2, 10, 'regular'),
(2, 2, 11, 'regular'),
(2, 2, 12, 'regular'),
(2, 3, 1, 'vip'),
(2, 3, 2, 'vip'),
(2, 3, 3, 'vip'),
(2, 3, 4, 'vip'),
(2, 3, 5, 'vip'),
(2, 3, 6, 'vip'),
(2, 3, 7, 'vip'),
(2, 3, 8, 'vip'),
(2, 3, 9, 'vip'),
(2, 3, 10, 'vip'),
(2, 3, 11, 'vip'),
(2, 3, 12, 'vip'),
(2, 4, 1, 'vip'),
(2, 4, 2, 'vip'),
(2, 4, 3, 'vip'),
(2, 4, 4, 'vip'),
(2, 4, 5, 'vip'),
(2, 4, 6, 'vip'),
(2, 4, 7, 'vip'),
(2, 4, 8, 'vip'),
(2, 4, 9, 'vip'),
(2, 4, 10, 'vip'),
(2, 4, 11, 'vip'),
(2, 4, 12, 'vip'),

-- Зал 3 (аналогично)
(3, 1, 1, 'regular'),
(3, 1, 2, 'regular'),
(3, 1, 3, 'regular'),
(3, 1, 4, 'regular'),
(3, 1, 5, 'regular'),
(3, 1, 6, 'regular'),
(3, 1, 7, 'regular'),
(3, 1, 8, 'regular'),
(3, 1, 9, 'regular'),
(3, 1, 10, 'regular'),
(3, 1, 11, 'regular'),
(3, 1, 12, 'regular'),
(3, 2, 1, 'regular'),
(3, 2, 2, 'regular'),
(3, 2, 3, 'regular'),
(3, 2, 4, 'regular'),
(3, 2, 5, 'regular'),
(3, 2, 6, 'regular'),
(3, 2, 7, 'regular'),
(3, 2, 8, 'regular'),
(3, 2, 9, 'regular'),
(3, 2, 10, 'regular'),
(3, 2, 11, 'regular'),
(3, 2, 12, 'regular'),
(3, 3, 1, 'vip'),
(3, 3, 2, 'vip'),
(3, 3, 3, 'vip'),
(3, 3, 4, 'vip'),
(3, 3, 5, 'vip'),
(3, 3, 6, 'vip'),
(3, 3, 7, 'vip'),
(3, 3, 8, 'vip'),
(3, 3, 9, 'vip'),
(3, 3, 10, 'vip'),
(3, 3, 11, 'vip'),
(3, 3, 12, 'vip'),
(3, 4, 1, 'vip'),
(3, 4, 2, 'vip'),
(3, 4, 3, 'vip'),
(3, 4, 4, 'vip'),
(3, 4, 5, 'vip'),
(3, 4, 6, 'vip'),
(3, 4, 7, 'vip'),
(3, 4, 8, 'vip'),
(3, 4, 9, 'vip'),
(3, 4, 10, 'vip'),
(3, 4, 11, 'vip'),
(3, 4, 12, 'vip');

INSERT INTO sessions (hall_id, movie_id, start_time, end_time, price_regular, price_vip)
VALUES
-- Сеансы в Зале 1
(1, 1, '2025-03-17 12:00:00', '2025-03-17 14:45:00', 500.00, 800.00),
(1, 2, '2025-03-17 15:30:00', '2025-03-17 18:30:00', 600.00, 900.00),
(1, 3, '2025-03-17 19:00:00', '2025-03-17 22:12:00', 700.00, 1000.00),

-- Сеансы в Зале 2
(2, 4, '2025-03-17 11:00:00', '2025-03-17 13:49:00', 450.00, 750.00),
(2, 5, '2025-03-17 14:30:00', '2025-03-17 17:26:00', 550.00, 850.00),
(2, 1, '2025-03-17 18:00:00', '2025-03-17 20:45:00', 500.00, 800.00),

-- Сеансы в Зале 3
(3, 2, '2025-03-17 10:30:00', '2025-03-17 13:30:00', 600.00, 900.00),
(3, 3, '2025-03-17 14:00:00', '2025-03-17 17:12:00', 700.00, 1000.00),
(3, 4, '2025-03-17 18:00:00', '2025-03-17 20:49:00', 550.00, 850.00);

INSERT INTO orders (user_id, total_amount)
VALUES (1, 1600.00), -- 2 VIP билета
       (2, 1000.00), -- 2 обычных билета
       (3, 900.00),  -- 1 VIP + 1 обычный
       (4, 500.00),  -- 1 обычный
       (5, 800.00); -- 1 VIP

INSERT INTO tickets (order_id, session_id, hall_id, row_number, seat_number, seat_type, price)
VALUES
-- Заказ 1 (2 VIP билета на "Дюна 2" в Зале 1)
(1, 1, 1, 3, 5, 'vip', 800.00),
(1, 1, 1, 3, 6, 'vip', 800.00),

-- Заказ 2 (2 обычных билета на "Оппенгеймера" в Зале 1)
(2, 2, 1, 1, 3, 'regular', 500.00),
(2, 2, 1, 1, 4, 'regular', 500.00),

-- Заказ 3 (1 VIP + 1 обычный на "Аватар 2" в Зале 3)
(3, 8, 3, 3, 7, 'vip', 1000.00),
(3, 8, 3, 1, 8, 'regular', 700.00),

-- Заказ 4 (1 обычный билет на "Джон Уик 4" в Зале 2)
(4, 4, 2, 2, 10, 'regular', 450.00),

-- Заказ 5 (1 VIP билет на "Бэтмена" в Зале 2)
(5, 5, 2, 4, 6, 'vip', 800.00);

-- endregion

-- region Наполнение БД до 10000 строк

INSERT INTO users (email)
SELECT 'user' || generate_series(1, 1000) || '@example.com';

INSERT INTO sessions (hall_id, movie_id, start_time, end_time, price_regular, price_vip)
SELECT
    (floor(random() * 3) + 1)::INT AS hall_id,  -- Генерация только 1, 2 или 3
    (floor(random() * 5) + 1)::INT AS movie_id,  -- 5 фильмов
    NOW() + (random() * INTERVAL '30 days') AS start_time,
    NOW() + (random() * INTERVAL '30 days') + INTERVAL '2 hours' AS end_time,
    (random() * 200 + 500)::INT AS price_regular,
    (random() * 300 + 800)::INT AS price_vip
FROM generate_series(1, 2000);

INSERT INTO orders (user_id, order_time, total_amount)
SELECT
    floor(random() * 1000) + 1,
    NOW() - (random() * interval '30 days'),
    (random() * 300 + 800)::INT  -- случайная сумма от 800 до 1100
FROM generate_series(1, 5000);  -- создаем 5000 заказов

INSERT INTO tickets (order_id, session_id, hall_id, row_number, seat_number, seat_type, price)
SELECT
    o.id AS order_id,
    s.id AS session_id,
    se.hall_id,
    se.row_number,
    se.seat_number,
    CASE WHEN random() > 0.8 THEN 'vip' ELSE 'regular' END AS seat_type,
    (random() * 300 + 800)::INT AS price
FROM sessions s
         JOIN seats se ON s.hall_id = se.hall_id
         JOIN orders o ON o.id = (SELECT id FROM orders ORDER BY random() LIMIT 1)
ORDER BY random()
LIMIT 10000
ON CONFLICT (session_id, row_number, seat_number) DO NOTHING; -- создаем 10000 билетов

-- endregion


-- region Наполнение БД до 1000000 строк

INSERT INTO users (email)
SELECT 'user' || generate_series(1, 100000) || '@example.com';

INSERT INTO movies (title, description, release_date, duration_minutes)
SELECT 'Фильм ' || generate_series(1, 50),
       'Описание фильма ' || generate_series(1, 50),
       NOW() - (random() * INTERVAL '365 days'),
       floor(random() * 120 + 80)
FROM generate_series(1, 50);

INSERT INTO sessions (hall_id, movie_id, start_time, end_time, price_regular, price_vip)
SELECT
    (floor(random() * 3) + 1)::INT,
    (floor(random() * 50) + 1)::INT,
    NOW() + (random() * INTERVAL '30 days'),
    NOW() + (random() * INTERVAL '30 days') + INTERVAL '2 hours',
    floor(random() * 500 + 300),
    floor(random() * 800 + 500)
FROM generate_series(1, 200000);

INSERT INTO orders (user_id, order_time, total_amount)
SELECT
    floor(random() * 1000) + 1,
    NOW() - (random() * interval '30 days'),
    (random() * 300 + 800)::INT  -- случайная сумма от 800 до 1100
FROM generate_series(1, 500000); -- создаем 500000 заказов

INSERT INTO tickets (order_id, session_id, hall_id, row_number, seat_number, seat_type, price)
SELECT
    o.id AS order_id,
    s.id AS session_id,
    se.hall_id,
    se.row_number,
    se.seat_number,
    CASE WHEN random() > 0.8 THEN 'vip' ELSE 'regular' END AS seat_type,
    (random() * 300 + 800)::INT AS price
FROM sessions s
         JOIN seats se ON s.hall_id = se.hall_id
         JOIN orders o ON o.id = (SELECT id FROM orders ORDER BY random() LIMIT 1)
ORDER BY random()
LIMIT 1000000
ON CONFLICT (session_id, row_number, seat_number) DO NOTHING; -- создаем 1000000 билетов

-- endregion
