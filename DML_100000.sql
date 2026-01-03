DO $$ 
DECLARE
    -- Конфигурация
    customers_count CONSTANT INT := 10000;  
    days_history    CONSTANT INT := 90;     
    tickets_target  CONSTANT INT := 100000; 
    
    -- ПЕРЕИМЕНОВАЛИ ПЕРЕМЕННУЮ, ЧТОБЫ УБРАТЬ КОНФЛИКТ
    v_total_count INT; 
BEGIN
    RAISE NOTICE 'Начало генерации данных...';

    -- 1. ПОЛНАЯ ОЧИСТКА
    TRUNCATE 
        public.ticket_discounts, 
        public.tickets, 
        public.screenings, 
        public.seats, 
        public.halls, 
        public.customers, 
        public.price_rules, 
        public.discounts
    RESTART IDENTITY CASCADE;

    -- 2. СПРАВОЧНИКИ
    INSERT INTO public.price_rules (name, hall_type, seat_type, time_type, base_price, valid_from)
    VALUES 
        ('Утренний эконом', '2D', 'standard', 'morning', 250.00, '2025-01-01'),
        ('День стандарт', '2D', 'standard', 'day', 350.00, '2025-01-01'),
        ('Вечерний блокбастер', '2D', 'standard', 'evening', 450.00, '2025-01-01'),
        ('IMAX Вечер', 'IMAX', 'standard', 'evening', 800.00, '2025-01-01'),
        ('IMAX VIP место', 'IMAX', 'vip', 'evening', 1200.00, '2025-01-01'),
        ('VIP зал', 'VIP', 'sofa', 'evening', 2500.00, '2025-01-01'),
        ('4DX Экшн', '4DX', 'standard', 'night', 1000.00, '2025-01-01');

    INSERT INTO public.discounts (name, discount_type, value, min_amount, valid_from, valid_to)
    VALUES 
        ('Студенческий билет', 'percentage', 20.00, 0, '2024-01-01', '2030-12-31'),
        ('Пенсионный', 'fixed', 150.00, 400.00, '2024-01-01', '2030-12-31'),
        ('Бонус постоянного клиента', 'percentage', 10.00, 1000.00, '2024-01-01', '2030-12-31');

    -- 3. ЗАЛЫ
    INSERT INTO public.halls (name, type, total_seats, schema)
    VALUES 
        ('Зал 1 (IMAX Laser)', 'IMAX', 120, '{"rows": 10, "cols": 12}'),
        ('Зал 2 (Standard)', '2D', 100, '{"rows": 10, "cols": 10}'),
        ('Зал 3 (Standard)', '2D', 100, '{"rows": 10, "cols": 10}'),
        ('Зал 4 (VIP Lounge)', 'VIP', 40, '{"rows": 5, "cols": 8}'),
        ('Зал 5 (4DX)', '4DX', 80, '{"rows": 8, "cols": 10}');

    INSERT INTO public.seats (hall_id, row_num, seat_num, seat_type)
    SELECT 
        h.id, r, s,
        CASE 
            WHEN h.type = 'VIP' THEN 'sofa'
            WHEN h.type = 'IMAX' AND r >= 8 THEN 'vip'
            ELSE 'standard' 
        END
    FROM public.halls h
    CROSS JOIN generate_series(1, 12) r
    CROSS JOIN generate_series(1, 12) s
    WHERE (r * s) <= h.total_seats;

    -- 4. КЛИЕНТЫ
    INSERT INTO public.customers (name, email, phone, birth_date)
    SELECT 
        'User_' || i, 
        'client_' || i || '@mail.test', 
        '+7' || (900 + (i % 99))::text || LPAD((i % 10000000)::text, 7, '0'),
        CURRENT_DATE - (floor(random() * 15000 + 6500) * interval '1 day')
    FROM generate_series(1, customers_count) i;

    -- 5. СЕАНСЫ
    INSERT INTO public.screenings (movie_id, hall_id, start_time, end_time, type)
    SELECT 
        m.id, 
        h.id, 
        t, 
        t + (m.duration_min * interval '1 minute'),
        CASE 
            WHEN extract(hour from t) < 12 THEN 'morning'
            WHEN extract(hour from t) < 17 THEN 'day'
            WHEN extract(hour from t) < 22 THEN 'evening'
            ELSE 'night'
        END
    FROM public.halls h
    CROSS JOIN generate_series(
        CURRENT_DATE::timestamp - (days_history || ' days')::interval, 
        CURRENT_DATE::timestamp + interval '2 days', 
        interval '3 hours 30 minutes'
    ) t
    CROSS JOIN LATERAL (
        SELECT id, duration_min FROM public.movies ORDER BY random() LIMIT 1
    ) m
    WHERE extract(hour from t) BETWEEN 10 AND 25
    ON CONFLICT DO NOTHING;

    -- 6. БИЛЕТЫ
    INSERT INTO public.tickets (screening_id, seat_id, customer_id, price, status, payment_method, purchase_time)
    SELECT 
        scr.id,
        st.id,
        (floor(random() * customers_count + 1))::int,
        COALESCE(
            (SELECT base_price FROM public.price_rules pr 
             WHERE pr.hall_type = h.type 
               AND pr.time_type = scr.type 
             LIMIT 1), 
            400.00
        ),
        'sold',
        (ARRAY['card', 'cash', 'sbp'])[floor(random()*3+1)],
        scr.start_time - (floor(random() * 7200) * interval '1 minute')
    FROM public.screenings scr
    JOIN public.halls h ON scr.hall_id = h.id
    JOIN public.seats st ON st.hall_id = h.id
    WHERE random() < 0.65 
    LIMIT tickets_target;

    -- 7. СКИДКИ
    INSERT INTO public.ticket_discounts (ticket_id, discount_id, applied_amount)
    SELECT 
        t.id, 
        d.id,
        CASE 
            WHEN d.discount_type = 'percentage' THEN t.price * (d.value / 100)
            ELSE LEAST(d.value, t.price)
        END
    FROM public.tickets t
    CROSS JOIN (SELECT id, discount_type, value FROM public.discounts ORDER BY random() LIMIT 1) d
    WHERE random() < 0.25;

    -- 8. ИТОГ
    SELECT count(*) INTO v_total_count FROM public.tickets;
    
    RAISE NOTICE 'Генерация завершена успешно!';
    RAISE NOTICE 'Всего билетов: %', v_total_count;
END $$;