-- ДЗ-9 «Индексирование данных»
-- Наполнение БД кинотеатра тестовыми данными: суммарно ~10 000 000 строк
--
-- Выполняется на ПУСТОЙ базе сразу после 01_schema.sql
-- (это второе состояние БД из задания; состояние ~10 000 строк даёт 02_fill_10k.sql)
--
-- seat_categories   3
-- movies            1 000
-- halls             50
-- seats             50 000   (50 залов x 1000 мест)
-- screenings        400 000
-- screening_prices  1 200 000 (3 категории на сеанс)
-- tickets           8 000 000 (20 мест из 1000 на сеанс)
-- ------------------------------------
-- итого ~9 651 053 строки
--
-- ВНИМАНИЕ: скрипт выполняется долго (десятки минут), требует ~3-4 ГБ диска.

SELECT setseed(0.42);

INSERT INTO seat_categories (name, description)
VALUES ('Стандарт', 'Обычное место'),
       ('VIP', 'Место повышенной комфортности'),
       ('Диван', 'Диван для двоих');

INSERT INTO movies (title, duration_minutes, description, release_year)
SELECT 'Фильм №' || g,
       80 + g % 80,
       'Описание фильма номер ' || g || '. Жанр: ' ||
       (ARRAY['комедия', 'драма', 'боевик', 'триллер', 'фантастика', 'мультфильм'])[1 + g % 6],
       1990 + g % 36
FROM generate_series(1, 1000) AS g;

INSERT INTO halls (name)
SELECT 'Зал №' || g
FROM generate_series(1, 50) AS g;

-- 1000 мест в зале: 25 рядов по 40 мест
INSERT INTO seats (hall_id, seat_category_id, row_number, place_number)
SELECT h.id,
       CASE WHEN r.row_number <= 5 THEN 2     -- VIP - первые пять рядов
            WHEN r.row_number <= 22 THEN 1    -- Стандарт
            ELSE 3 END,                       -- Диван - последние ряды
       r.row_number,
       p.place_number
FROM halls h
         CROSS JOIN generate_series(1, 25) AS r(row_number)
         CROSS JOIN generate_series(1, 40) AS p(place_number);

-- 400 000 сеансов за 120 дней: ~3333 сеанса в день по 50 залам, часть - сегодня
INSERT INTO screenings (movie_id, hall_id, start_time)
SELECT 1 + (g * 7) % 1000,
       1 + g % 50,
       date_trunc('day', now())
           + (((g / 3333) % 120) - 60) * interval '1 day'
           + (8 + (g % 14)) * interval '1 hour'
           + ((g % 3) * 20) * interval '1 minute'
FROM generate_series(1, 400000) AS g;

-- цены для каждой категории на каждый сеанс
INSERT INTO screening_prices (screening_id, seat_category_id, price)
SELECT s.id, c.id,
       (200 + (s.id % 6) * 25) * CASE c.id WHEN 1 THEN 1.0 WHEN 2 THEN 2.0 ELSE 3.5 END
FROM screenings s
         CROSS JOIN seat_categories c;

-- 8 млн билетов: 20 из 1000 мест каждого зала продано на сеанс.
-- Вставка тремя порциями, чтобы не раздувать одну транзакцию.
INSERT INTO tickets (screening_id, seat_id, customer_email, sold_at, final_price)
SELECT s.id,
       st.id,
       CASE WHEN random() < 0.1
           THEN NULL
           ELSE 'user' || (1 + floor(random() * 1000000))::int || '@example.com' END,
       s.start_time - (random() * interval '5 days'),
       sp.price
FROM screenings s
         JOIN seats st ON st.hall_id = s.hall_id AND (st.row_number * 40 + st.place_number + s.id * 7) % 50 = 0
         JOIN screening_prices sp
              ON sp.screening_id = s.id AND sp.seat_category_id = st.seat_category_id
WHERE s.id <= 100000;

INSERT INTO tickets (screening_id, seat_id, customer_email, sold_at, final_price)
SELECT s.id,
       st.id,
       CASE WHEN random() < 0.1
           THEN NULL
           ELSE 'user' || (1 + floor(random() * 1000000))::int || '@example.com' END,
       s.start_time - (random() * interval '5 days'),
       sp.price
FROM screenings s
         JOIN seats st ON st.hall_id = s.hall_id AND (st.row_number * 40 + st.place_number + s.id * 7) % 50 = 0
         JOIN screening_prices sp
              ON sp.screening_id = s.id AND sp.seat_category_id = st.seat_category_id
WHERE s.id > 100000 AND s.id <= 300000;

INSERT INTO tickets (screening_id, seat_id, customer_email, sold_at, final_price)
SELECT s.id,
       st.id,
       CASE WHEN random() < 0.1
           THEN NULL
           ELSE 'user' || (1 + floor(random() * 1000000))::int || '@example.com' END,
       s.start_time - (random() * interval '5 days'),
       sp.price
FROM screenings s
         JOIN seats st ON st.hall_id = s.hall_id AND (st.row_number * 40 + st.place_number + s.id * 7) % 50 = 0
         JOIN screening_prices sp
              ON sp.screening_id = s.id AND sp.seat_category_id = st.seat_category_id
WHERE s.id > 300000;

ANALYZE;
