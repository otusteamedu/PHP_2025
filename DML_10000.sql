DO $$ 
DECLARE
    row_count INT;
BEGIN
    -- 1. ПОЛНАЯ ОЧИСТКА
    TRUNCATE 
        public.ticket_discounts, public.tickets, public.screenings, 
        public.seats, public.halls, public.customers,
        public.price_rules, public.discounts
    RESTART IDENTITY CASCADE;

    -- 2. ЗАПОЛНЕНИЕ ПРАВИЛ ЦЕН (Price Rules)
    INSERT INTO public.price_rules (name, hall_type, seat_type, time_type, base_price, valid_from)
    VALUES 
        ('Утренний стандарт', '2D', 'standard', 'morning', 250.00, '2025-01-01'),
        ('Вечерний IMAX', 'IMAX', 'standard', 'evening', 600.00, '2025-01-01'),
        ('VIP вечер', 'VIP', 'vip', 'evening', 1200.00, '2025-01-01'),
        ('Детский день', '2D', 'standard', 'day', 200.00, '2025-01-01'),
        ('Ночной 4DX', '4DX', 'standard', 'night', 500.00, '2025-01-01');

    -- 3. ЗАПОЛНЕНИЕ СКИДОК (Discounts)
    INSERT INTO public.discounts (name, discount_type, value, min_amount, valid_from, valid_to)
    VALUES 
        ('Студенческая', 'percentage', 15.00, 0, '2025-01-01', '2026-12-31'),
        ('Пенсионная', 'fixed', 100.00, 300.00, '2025-01-01', '2026-12-31'),
        ('Новогодний сейл', 'percentage', 20.00, 500.00, '2025-12-15', '2026-01-15');

    -- 4. ИНФРАСТРУКТУРА (Залы и Места)
    INSERT INTO public.halls (name, type, total_seats, schema)
    VALUES 
        ('Зал 1 (IMAX)', 'IMAX', 100, '{"rows": 10, "cols": 10}'),
        ('Зал 2 (Standard)', '2D', 100, '{"rows": 10, "cols": 10}'),
        ('Зал 3 (VIP)', 'VIP', 100, '{"rows": 10, "cols": 10}');

    INSERT INTO public.seats (hall_id, row_num, seat_num, seat_type)
    SELECT h.id, r, s, CASE WHEN h.type = 'VIP' OR r > 8 THEN 'vip' ELSE 'standard' END
    FROM public.halls h, generate_series(1, 10) r, generate_series(1, 10) s;

    -- 5. КЛИЕНТЫ
    INSERT INTO public.customers (name, email, phone, birth_date)
    SELECT 'Customer_' || i, 'user' || i || '@cinema.com', '+79' || LPAD(i::text, 9, '0'), '1990-01-01'::date + i
    FROM generate_series(1, 500) i;

    -- 6. СЕАНСЫ
    INSERT INTO public.screenings (movie_id, hall_id, start_time, end_time, type)
    SELECT 
        m.id, h.id, 
        t, t + (m.duration_min * interval '1 minute'),
        (ARRAY['morning', 'day', 'evening', 'night'])[floor(random()*4+1)]
    FROM public.movies m
    JOIN public.halls h ON h.id = (m.id % 3 + 1)
    CROSS JOIN generate_series(CURRENT_DATE::timestamp, CURRENT_DATE::timestamp + interval '7 days', interval '4 hours') t
    ON CONFLICT DO NOTHING;

    -- 7. БИЛЕТЫ (Генерируем ~10 000 записей)
    INSERT INTO public.tickets (screening_id, seat_id, customer_id, price, status, payment_method)
    SELECT 
        scr.id, st.id, floor(random()*499+1),
        COALESCE((SELECT base_price FROM public.price_rules WHERE hall_type = h.type LIMIT 1), 400.00),
        'sold', 'card'
    FROM public.screenings scr
    JOIN public.halls h ON scr.hall_id = h.id
    JOIN public.seats st ON st.hall_id = h.id
    WHERE random() < 0.8 
    LIMIT 11000;

    -- 8. СКИДКИ В БИЛЕТАХ (Ticket Discounts)
    -- Применяем случайные скидки к 30% проданных билетов
    INSERT INTO public.ticket_discounts (ticket_id, discount_id, applied_amount)
    SELECT 
        t.id, 
        d.id,
        CASE 
            WHEN d.discount_type = 'percentage' THEN t.price * (d.value / 100)
            ELSE d.value
        END
    FROM public.tickets t
    CROSS JOIN (SELECT id, discount_type, value FROM public.discounts ORDER BY random() LIMIT 1) d
    WHERE random() < 0.4;

    RAISE NOTICE 'База полностью заполнена!';
END $$;
