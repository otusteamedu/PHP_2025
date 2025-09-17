BEGIN;
TRUNCATE TABLE ticket, payment, showtime, seat, hall, cinema, customer, movie, seat_type RESTART IDENTITY CASCADE;
INSERT INTO seat_type (code, name, price_multiplier, description) VALUES
  ('STD',  'Standard',   1.000, 'Regular seat'),
  ('VIP',  'VIP',        1.500, 'Premium seat'),
  ('LOVE', 'Love Seat',  1.200, 'Double/sofa seat'),
  ('ACC',  'Accessible', 0.800, 'Accessible seat');
INSERT INTO cinema (id, name, address, timezone) VALUES
  (1, 'Cinema North', 'Lenin Ave 1, Moscow', 'Europe/Moscow'),
  (2, 'Cinema South', 'Nevsky 25, Saint-Petersburg', 'Europe/Moscow');

INSERT INTO hall (cinema_id, name, capacity, is_imax) VALUES
  (1, 'Hall 1', 12, FALSE),
  (1, 'Hall 2', 16, FALSE),
  (1, 'Hall 3', 20, FALSE),
  (1, 'Hall 4', 24, FALSE),
  (1, 'Hall 5', 28, TRUE);

INSERT INTO hall (cinema_id, name, capacity, is_imax) VALUES
  (2, 'Hall 1', 12, FALSE),
  (2, 'Hall 2', 16, FALSE),
  (2, 'Hall 3', 20, FALSE),
  (2, 'Hall 4', 24, FALSE),
  (2, 'Hall 5', 28, TRUE);

WITH hall_info AS (
  SELECT h.id AS hall_id, h.capacity, ((h.capacity + 4) / 5) AS row_cnt
  FROM hall h
), seats AS (
  SELECT
    hi.hall_id,
    ((gs - 1) / 5) + 1 AS row_number,
    ((gs - 1) % 5) + 1 AS seat_number,
  hi.row_cnt,
  gs
FROM hall_info hi
  CROSS JOIN LATERAL generate_series(1, hi.capacity) AS gs
  )
INSERT INTO seat (hall_id, row_number, seat_number, seat_type_id, x, y)
SELECT
  s.hall_id,
  s.row_number,
  s.seat_number,
  CASE
    WHEN s.row_number >= GREATEST(s.row_cnt - 1, 1) THEN (SELECT id FROM seat_type WHERE code = 'VIP')
    WHEN s.seat_number IN (1, 5)                      THEN (SELECT id FROM seat_type WHERE code = 'ACC')
    ELSE                                                   (SELECT id FROM seat_type WHERE code = 'STD')
    END AS seat_type_id,
  (s.seat_number - 1) * 2 AS x,
  (s.row_number - 1)      AS y
FROM seats s;

INSERT INTO movie (title, release_date, duration_min, age_rating)
VALUES
  ('Sky Patrol',        DATE '2024-07-05', 105, '12+'),
  ('Deep Ocean 2',      DATE '2024-06-12', 118, '12+'),
  ('Quantum Heist',     DATE '2024-05-01',  99, '16+'),
  ('Forest of Echoes',  DATE '2023-12-21', 122, '12+'),
  ('Little Comet',      DATE '2025-01-10',  95, '6+');

WITH base AS (
  SELECT
    h.id AS hall_id,
    h.is_imax,
    gs AS slot
  FROM hall h
  CROSS JOIN generate_series(1, 3) AS gs
), plan AS (
  SELECT
    b.hall_id,
    ((b.hall_id + b.slot - 1) % 5) + 1 AS movie_id,
    (date_trunc('day', now()) + (b.slot || ' days')::interval
       + make_interval(hours => CASE b.slot WHEN 1 THEN 12 WHEN 2 THEN 16 ELSE 20 END)) AS start_time,
    CASE WHEN b.is_imax THEN 'IMAX' ELSE '2D' END AS format,
    'RU' AS language,
    (CASE WHEN b.is_imax THEN 600 ELSE 400 END) + b.slot * 50 AS base_price
  FROM base b
)
INSERT INTO showtime (hall_id, movie_id, start_time, end_time, base_price, language, format)
SELECT
  p.hall_id,
  p.movie_id,
  p.start_time,
  p.start_time + make_interval(mins => m.duration_min),
  p.base_price,
  p.language,
  p.format
FROM plan p
JOIN movie m ON m.id = p.movie_id;

INSERT INTO customer (full_name, email, phone)
SELECT
  FORMAT('Customer %03s', gs) AS full_name,
  FORMAT('user%03s@example.com', gs) AS email,
  FORMAT('+7000000%03s', gs) AS phone
FROM generate_series(1, 250) AS gs;

INSERT INTO ticket (
  showtime_id, seat_id, customer_id, status, reserved_at, sold_at, price_amount, refund_amount, currency, payment_id
)
SELECT
  st.id AS showtime_id,
  s.id  AS seat_id,
  ((ROW_NUMBER() OVER (ORDER BY st.id, s.id) - 1) % 250) + 1 AS customer_id,
  'sold'::text AS status,
  now() - INTERVAL '1 day' AS reserved_at,
  now() AS sold_at,
  ROUND(st.base_price * stt.price_multiplier, 2) AS price_amount,
  NULL::numeric AS refund_amount,
  'RUB'::char(3) AS currency,
  NULL::bigint AS payment_id
FROM showtime st
JOIN seat s       ON s.hall_id = st.hall_id
JOIN seat_type stt ON stt.id = s.seat_type_id;

COMMIT;
