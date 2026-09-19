-- ДЗ-9 «Индексирование данных»
-- Наполнение БД кинотеатра тестовыми данными: суммарно ~10 000 строк
--
-- seat_categories 3
-- movies          200
-- halls           10
-- seats           1 000   (10 залов x 100 мест)
-- screenings      800     (период: -30 ... +30 дней от текущей даты)
-- screening_prices 2 400  (3 категории на каждый сеанс)
-- tickets         ~5 700
-- ------------------------------------
-- итого ~10 100 строк

-- детерминированный random, чтобы данные были воспроизводимыми
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
FROM generate_series(1, 200) AS g;

INSERT INTO halls (name)
SELECT 'Зал №' || g
FROM generate_series(1, 10) AS g;

-- 100 мест в зале: 10 рядов по 10 мест
INSERT INTO seats (hall_id, seat_category_id, row_number, place_number)
SELECT h.id,
       CASE WHEN r.row_number <= 2 THEN 2     -- VIP - первые два ряда
            WHEN r.row_number <= 9 THEN 1     -- Стандарт
            ELSE 3 END,                       -- Диван - последний ряд
       r.row_number,
       p.place_number
FROM halls h
         CROSS JOIN generate_series(1, 10) AS r(row_number)
         CROSS JOIN generate_series(1, 10) AS p(place_number);

-- 800 сеансов: 10 залов, 60 дней (13-14 сеансов в день), часть - сегодня
INSERT INTO screenings (movie_id, hall_id, start_time)
SELECT 1 + (g * 7) % 200,
       1 + g % 10,
       date_trunc('day', now())
           + (((g / 10) % 60) - 30) * interval '1 day'
           + (10 + (g % 10)) * interval '1 hour'
           + ((g % 3) * 20) * interval '1 minute'
FROM generate_series(1, 800) AS g;

-- цены для каждой категории на каждый сеанс
INSERT INTO screening_prices (screening_id, seat_category_id, price)
SELECT s.id, c.id,
       (200 + (s.id % 6) * 25) * CASE c.id WHEN 1 THEN 1.0 WHEN 2 THEN 2.0 ELSE 3.5 END
FROM screenings s
         CROSS JOIN seat_categories c;

-- билеты: ~1/14 мест каждого зала продано на каждый сеанс
-- (маска по row_number/place_number, т.к. id мест не идут подряд в пределах зала)
INSERT INTO tickets (screening_id, seat_id, customer_email, sold_at, final_price)
SELECT s.id,
       st.id,
       CASE WHEN random() < 0.1
           THEN NULL
           ELSE 'user' || (1 + floor(random() * 100000))::int || '@example.com' END,
       s.start_time - (random() * interval '5 days'),
       sp.price
FROM screenings s
         JOIN seats st ON st.hall_id = s.hall_id
              AND (st.row_number * 10 + st.place_number + s.id * 7) % 14 = 0
         JOIN screening_prices sp
              ON sp.screening_id = s.id AND sp.seat_category_id = st.seat_category_id;

ANALYZE;
