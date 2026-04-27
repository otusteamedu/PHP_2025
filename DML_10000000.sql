DO $$
DECLARE
    customers_count CONSTANT INT := 200000;
    tickets_target  CONSTANT BIGINT := 10000000;
    v_tickets_count BIGINT;
BEGIN
    RAISE NOTICE 'Start loading 10M dataset...';

    -- 1) Полная очистка динамических таблиц
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

    -- 2) Справочники
    INSERT INTO public.price_rules (name, hall_type, seat_type, time_type, base_price, valid_from)
    VALUES
        ('2D Morning Standard', '2D', 'standard', 'morning', 250.00, '2025-01-01'),
        ('2D Day Standard', '2D', 'standard', 'day', 320.00, '2025-01-01'),
        ('2D Evening Standard', '2D', 'standard', 'evening', 430.00, '2025-01-01'),
        ('IMAX Day Standard', 'IMAX', 'standard', 'day', 650.00, '2025-01-01'),
        ('IMAX Evening VIP', 'IMAX', 'vip', 'evening', 1100.00, '2025-01-01'),
        ('4DX Night Standard', '4DX', 'standard', 'night', 900.00, '2025-01-01'),
        ('VIP Evening Sofa', 'VIP', 'sofa', 'evening', 1800.00, '2025-01-01');

    INSERT INTO public.discounts (name, discount_type, value, min_amount, valid_from, valid_to)
    VALUES
        ('Student 20%', 'percentage', 20.00, 0, '2025-01-01', '2030-12-31'),
        ('Loyalty 10%', 'percentage', 10.00, 700.00, '2025-01-01', '2030-12-31'),
        ('Fixed 150', 'fixed', 150.00, 500.00, '2025-01-01', '2030-12-31');

    -- 3) Инфраструктура: 25 залов
    WITH hall_def AS (
        SELECT
            i,
            CASE
                WHEN i % 5 = 0 THEN 'VIP'
                WHEN i % 4 = 0 THEN 'IMAX'
                WHEN i % 3 = 0 THEN '4DX'
                ELSE '2D'
            END AS hall_type,
            CASE WHEN i % 5 = 0 THEN 12 ELSE 20 END AS rows_cnt,
            CASE WHEN i % 5 = 0 THEN 12 ELSE 20 END AS cols_cnt
        FROM generate_series(1, 25) AS gs(i)
    )
    INSERT INTO public.halls (name, type, total_seats, schema)
    SELECT
        'Hall ' || i || ' (' || hall_type || ')',
        hall_type,
        rows_cnt * cols_cnt,
        json_build_object('rows', rows_cnt, 'cols', cols_cnt)::json
    FROM hall_def;

    -- 4) Места
    INSERT INTO public.seats (hall_id, row_num, seat_num, seat_type)
    SELECT
        h.id,
        r,
        c,
        CASE
            WHEN h.type = 'VIP' THEN 'sofa'
            WHEN h.type = 'IMAX' AND r >= ((h.schema->>'rows')::int - 2) THEN 'vip'
            WHEN r = 1 AND c IN (1, 2) THEN 'handicap'
            ELSE 'standard'
        END
    FROM public.halls h
    JOIN generate_series(1, 20) AS r ON r <= (h.schema->>'rows')::int
    JOIN generate_series(1, 20) AS c ON c <= (h.schema->>'cols')::int;

    -- 5) Клиенты
    INSERT INTO public.customers (name, email, phone, birth_date)
    SELECT
        'Customer_' || i,
        'customer_' || i || '@mail.test',
        '+79' || LPAD((100000000 + i)::text, 9, '0'),
        DATE '1970-01-01' + ((i % 17000))
    FROM generate_series(1, customers_count) AS gs(i);

    -- 6) Сеансы (~49 500)
    WITH movie_stats AS (
        SELECT COUNT(*) AS cnt FROM public.movies
    ),
    schedule AS (
        SELECT
            h.id AS hall_id,
            (d::date + t.show_time)::timestamp AS start_time,
            CASE
                WHEN EXTRACT(HOUR FROM t.show_time) < 12 THEN 'morning'
                WHEN EXTRACT(HOUR FROM t.show_time) < 17 THEN 'day'
                WHEN EXTRACT(HOUR FROM t.show_time) < 22 THEN 'evening'
                ELSE 'night'
            END AS show_type
        FROM public.halls h
        CROSS JOIN generate_series(
            CURRENT_DATE - INTERVAL '365 days',
            CURRENT_DATE + INTERVAL '30 days',
            INTERVAL '1 day'
        ) AS d
        CROSS JOIN (VALUES
            ('09:00'::time),
            ('12:00'::time),
            ('15:00'::time),
            ('18:00'::time),
            ('21:00'::time)
        ) AS t(show_time)
    ),
    schedule_with_movie AS (
        SELECT
            s.hall_id,
            s.start_time,
            s.show_type,
            ((s.hall_id + EXTRACT(DOY FROM s.start_time)::int + EXTRACT(HOUR FROM s.start_time)::int) % ms.cnt) + 1 AS movie_id
        FROM schedule s
        CROSS JOIN movie_stats ms
    )
    INSERT INTO public.screenings (movie_id, hall_id, start_time, end_time, type)
    SELECT
        swm.movie_id,
        swm.hall_id,
        swm.start_time,
        swm.start_time + (m.duration_min || ' minute')::interval,
        swm.show_type
    FROM schedule_with_movie swm
    JOIN public.movies m ON m.id = swm.movie_id
    ON CONFLICT (hall_id, start_time) DO NOTHING;

    -- 7) Билеты: ровно 10 000 000 без конфликтов (screening_id, seat_id)
    WITH seat_pool AS (
        SELECT
            scr.id AS screening_id,
            st.id AS seat_id,
            scr.start_time
        FROM public.screenings scr
        JOIN public.seats st ON st.hall_id = scr.hall_id
        ORDER BY scr.id, st.id
        LIMIT tickets_target
    )
    INSERT INTO public.tickets (
        screening_id,
        seat_id,
        customer_id,
        price,
        status,
        payment_method,
        purchase_time
    )
    SELECT
        sp.screening_id,
        sp.seat_id,
        ((sp.seat_id * 37 + sp.screening_id * 13) % customers_count) + 1,
        ROUND(
            (
                COALESCE(pr.base_price, 400.00)
                * CASE
                    WHEN (sp.seat_id + sp.screening_id) % 20 = 0 THEN 1.30
                    WHEN (sp.seat_id + sp.screening_id) % 11 = 0 THEN 1.15
                    ELSE 1.00
                  END
            )::numeric,
            2
        ) AS price,
        CASE
            WHEN (sp.seat_id + sp.screening_id) % 100 < 88 THEN 'sold'
            WHEN (sp.seat_id + sp.screening_id) % 100 < 95 THEN 'reserved'
            ELSE 'cancelled'
        END AS status,
        (ARRAY['card', 'cash', 'sbp'])[((sp.seat_id + sp.screening_id) % 3) + 1] AS payment_method,
        sp.start_time - (((sp.seat_id % 10080) + 30) || ' minutes')::interval AS purchase_time
    FROM seat_pool sp
    JOIN public.screenings scr ON scr.id = sp.screening_id
    JOIN public.halls h ON h.id = scr.hall_id
    LEFT JOIN LATERAL (
        SELECT pr.base_price
        FROM public.price_rules pr
        WHERE pr.hall_type = h.type
          AND pr.time_type = scr.type
        ORDER BY pr.valid_from DESC
        LIMIT 1
    ) pr ON TRUE;

    -- 8) Скидки на часть sold-билетов
    INSERT INTO public.ticket_discounts (ticket_id, discount_id, applied_amount)
    SELECT
        t.id,
        d.id,
        CASE
            WHEN d.discount_type = 'percentage' THEN ROUND((t.price * d.value / 100.0)::numeric, 2)
            ELSE LEAST(d.value, t.price)
        END AS applied_amount
    FROM public.tickets t
    JOIN public.discounts d ON d.id = ((t.id % 3) + 1)
    WHERE t.status = 'sold'
      AND t.id % 5 = 0;

    ANALYZE public.halls;
    ANALYZE public.seats;
    ANALYZE public.customers;
    ANALYZE public.screenings;
    ANALYZE public.tickets;
    ANALYZE public.ticket_discounts;

    SELECT COUNT(*) INTO v_tickets_count FROM public.tickets;
    RAISE NOTICE 'Done. Tickets generated: %', v_tickets_count;
END $$;
