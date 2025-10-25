-- 0. Создаём необходимые последовательности
CREATE SEQUENCE IF NOT EXISTS halls_id_seq START WITH 1000;
CREATE SEQUENCE IF NOT EXISTS seats_id_seq START WITH 10000;
CREATE SEQUENCE IF NOT EXISTS sessions_id_seq START WITH 10000;

-- Привязываем последовательности к таблицам
ALTER TABLE public.halls ALTER COLUMN id SET DEFAULT nextval('halls_id_seq');
ALTER TABLE public.seats ALTER COLUMN id SET DEFAULT nextval('seats_id_seq');
ALTER TABLE public.sessions ALTER COLUMN id SET DEFAULT nextval('sessions_id_seq');

-- 1. Генерация залов
INSERT INTO public.halls (title, seats_count, price_premium)
SELECT
  'Зал ' || g.i,
  300, -- 300 мест
  ROUND((0.8 + random() * 0.4)::numeric, 2) -- премия от 0.8 до 1.2
FROM generate_series(1, 100) AS g(i);

-- 2. Генерация мест в залах
WITH hall_list AS (
  SELECT id FROM public.halls
)
INSERT INTO public.seats (seat_row, seat_number, seat_class_id, hall_id)
SELECT
  (g.i / 15) + 1,          -- 15 мест в ряду
  (g.i % 15) + 1,
  1,                      -- класс комфорта id = 1
  h.id
FROM hall_list h
CROSS JOIN generate_series(0, 299) AS g(i);

-- 3. Генерация сессий
WITH hall_list AS (
  SELECT id FROM public.halls
),
film_list AS (
  SELECT id FROM public.films
)
INSERT INTO public.sessions (film_id, hall_id, price_premium, session_start, session_end)
SELECT
  (SELECT id FROM film_list ORDER BY random() LIMIT 1),
  (SELECT id FROM hall_list ORDER BY random() LIMIT 1),
  ROUND((0.8 + random() * 0.4)::numeric, 2),
  now() + (g.i || ' minutes')::interval,
  now() + (g.i + 120 || ' minutes')::interval
FROM generate_series(1, 10000) AS g(i);

-- 4. Генерация 1 000 000 билетов
WITH available_seats AS (
  SELECT
    s.id AS session_id,
    se.id AS seat_id
  FROM public.sessions s
  JOIN public.seats se ON se.hall_id = s.hall_id
),
tickets_to_insert AS (
  SELECT
    ROW_NUMBER() OVER () + (SELECT COALESCE(MAX(id), 0) FROM public.tickets) AS id, -- продолжаем id после уже существующих билетов
    av.session_id,
    av.seat_id
  FROM available_seats av
  ORDER BY random()
  LIMIT 1000000
)
INSERT INTO public.tickets (id, seat_id, session_id, final_price)
SELECT
  id,
  seat_id,
  session_id,
  ROUND((5 + random() * 10)::numeric, 2) -- цена от 5 до 15
FROM tickets_to_insert;
