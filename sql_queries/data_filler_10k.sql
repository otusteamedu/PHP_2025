-- Очистка таблиц перед заполнением
TRUNCATE TABLE tickets, sessions, seats, halls, seats_class, films RESTART IDENTITY CASCADE;

-- 1. seats_class (классы мест)
INSERT INTO public.seats_class (id, title, price_premium)
SELECT
    id,
    'Class ' || id,
    ROUND((0.8 + random() * 0.6)::numeric, 2) 
FROM generate_series(1, 5) AS id;

-- 2. halls (залы)
INSERT INTO public.halls (id, title, seats_count, price_premium)
SELECT
    id,
    'Hall ' || id,
    seats_count,
    ROUND((0.8 + random() * 0.5)::numeric, 2)  
FROM (
    SELECT id, (50 + (random() * 150)::int) AS seats_count   --до 150 мест в зале  
    FROM generate_series(1, 10) AS id
) halls_data;

-- 3. seats (места в залах)
-- сначала получаем все залы и для каждого зала создаем места
INSERT INTO public.seats (id, seat_row, seat_number, seat_class_id, hall_id)
SELECT
    ROW_NUMBER() OVER () AS id,
    (gs.row_num / 20) + 1, -- 20 мест в ряду
    (gs.row_num % 20) + 1,
    (random() * 4)::int + 1, -- класс от 1 до 5
    h.id -- зал
FROM (
    SELECT id, seats_count
    FROM public.halls
) h
JOIN LATERAL generate_series(1, h.seats_count) AS gs(row_num) ON true;

-- 4. films (фильмы)
INSERT INTO public.films (id, title, distribution_start, distribution_end, base_price)
SELECT
    id,
    'Film ' || id,
    NOW() - (interval '90 days') * random(),
    NOW() + (interval '90 days') * random(),
    ROUND((3 + random() * 7)::numeric, 2)
FROM generate_series(1, 50) AS id;

-- 5. sessions (сеансы)
INSERT INTO public.sessions (id, film_id, hall_id, price_premium, session_start, session_end)
SELECT
    gs.id,
    (random() * 49)::int + 1,  -- фильм id (1-50)
    (random() * 9)::int + 1,   -- зал id (1-10)
    ROUND((0.8 + random() * 0.4)::numeric, 2),
    NOW() + (interval '1 day') * (random() * 30),   -- старт в ближайший месяц
    NOW() + (interval '1 day') * (31 + random() * 30)   -- конец через 1-2 месяца
FROM generate_series(1, 10000) AS gs(id);

-- 6. tickets (билеты)
-- выберем существующие места и сеансы
INSERT INTO public.tickets (id, seat_id, session_id, final_price)
SELECT
    gs.id,
    (SELECT id FROM public.seats ORDER BY random() LIMIT 1),
    (SELECT id FROM public.sessions ORDER BY random() LIMIT 1),
    ROUND((5 + random() * 10)::numeric, 2)
FROM generate_series(1, 3000) AS gs(id);