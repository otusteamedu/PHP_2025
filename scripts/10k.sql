TRUNCATE
    tickets,
    seances,
    hall_seats,
    movies,
    halls
    RESTART IDENTITY CASCADE;

BEGIN;

--------------------------------------------------
-- 1. ЗАЛЫ
--------------------------------------------------
INSERT INTO halls (title)
SELECT 'Hall ' || i
FROM generate_series(1, 10000) i;

--------------------------------------------------
-- 2. МЕСТА В ЗАЛАХ
--------------------------------------------------
INSERT INTO hall_seats (hall_id, row, col, number, seat_type)
SELECT h.id,
       r,
       c,
       r || '-' || c,
       CASE
           WHEN r <= 2 THEN 'vip'
           ELSE 'standard'
           END
FROM halls h
         CROSS JOIN generate_series(1, 20) r
         CROSS JOIN generate_series(1, 20) c;

--------------------------------------------------
-- 3. ФИЛЬМЫ
--------------------------------------------------
INSERT INTO movies (title)
SELECT 'Movie ' || i
FROM generate_series(1, 10000) i;

--------------------------------------------------
-- 4. СЕАНСЫ
--------------------------------------------------
INSERT INTO seances (begin_at, end_at, hall_id, movie_id, price)
SELECT ts,
       ts + interval '2 hours',
       h.id,
       (SELECT id FROM movies ORDER BY random() LIMIT 1),
       (random() * 500 + 300)::int
FROM halls h
         CROSS JOIN generate_series(
    now(),
    now() + interval '10 days',
    interval '2 hours'
                    ) ts
LIMIT 10000;

--------------------------------------------------
-- 5. БИЛЕТЫ
--------------------------------------------------
INSERT INTO tickets (price, seance_id, hall_seat_id)
SELECT s.price,
       s.id,
       hs.id
FROM seances s
         JOIN hall_seats hs
              ON hs.hall_id = s.hall_id
WHERE random() < 0.75;

COMMIT;
