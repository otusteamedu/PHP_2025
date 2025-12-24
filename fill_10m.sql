-- Заполнение залов
INSERT INTO hall (name)
SELECT 'Зал_' || LPAD(i::text, 4, '0') || '_' ||
       CASE (i % 10)
           WHEN 0 THEN 'IMAX_Deluxe'
           WHEN 1 THEN 'VIP_Premium'
           WHEN 2 THEN 'Standard_Plus'
           WHEN 3 THEN '3D_Cinema'
           WHEN 4 THEN 'Dolby_Atmos'
           WHEN 5 THEN '4DX_Experience'
           WHEN 6 THEN 'Comfort_Hall'
           WHEN 7 THEN 'Family_Cinema'
           WHEN 8 THEN 'Economy_Class'
           ELSE 'Luxury_Suite'
           END
FROM generate_series(1, 500) AS i;

-- Заполнение фильмов
INSERT INTO movie (title)
SELECT 'Movie_' || LPAD(i::text, 5, '0') || '_' ||
       substring(md5(random()::text), 1, 30)
FROM generate_series(1, 10000) AS i;

-- Заполнение типов мест
INSERT INTO seat_type (type, price)
VALUES
    ('Эконом', 150.00),
    ('Стандарт', 250.00),
    ('Стандарт_Плюс', 350.00),
    ('Комфорт', 450.00),
    ('Комфорт_Плюс', 550.00),
    ('VIP', 650.00),
    ('VIP_Премиум', 800.00),
    ('Люкс', 1000.00),
    ('Люкс_Премиум', 1200.00),
    ('Королевский', 1500.00);

-- Заполнение мест
DO $$
    BEGIN
        INSERT INTO seat (hall_id, row_num, seat_num, seat_type_id)
        SELECT
            h.id,
            r.row_num,
            s.seat_num,
            CASE
                WHEN r.row_num <= 2 THEN 1
                WHEN r.row_num <= 4 THEN 2
                WHEN r.row_num <= 6 THEN 4
                WHEN r.row_num <= 8 THEN 6
                ELSE 8
                END
        FROM hall h
            CROSS JOIN generate_series(1, 10) AS r(row_num)
            CROSS JOIN generate_series(1, 30) AS s(seat_num);
END $$;

-- Заполнение сеансов
DO $$
    DECLARE
        batch_size INTEGER := 50000;
        total_batches INTEGER := 8;
        i INTEGER;
    BEGIN
        FOR i IN 0..(total_batches - 1) LOOP
            INSERT INTO showtime (hall_id, movie_id, time)
            SELECT
                ((n - 1) % 500) + 1,
                ((n - 1) % 10000) + 1,
                CURRENT_DATE - INTERVAL '200 days' +
                ((i * batch_size + n) * INTERVAL '3 minutes 30 seconds')
            FROM generate_series(1, batch_size) AS n;
        END LOOP;
END $$;

-- Заполнение покупателей
DO $$
    DECLARE
        batch_size INTEGER := 250000;
        total_batches INTEGER := 10;
        i INTEGER;
    BEGIN
        FOR i IN 0..(total_batches - 1) LOOP
            INSERT INTO customer (first_name, last_name, email, phone)
            SELECT
                'First_' || LPAD((i * batch_size + n)::text, 7, '0'),
                'Last_' || LPAD((i * batch_size + n)::text, 7, '0'),
                'customer' || (i * batch_size + n) || '@email' ||
                ((n % 100) + 1) || '.com',
                '+79' || LPAD(((i * batch_size + n) % 999999999)::text, 9, '0')
            FROM generate_series(1, batch_size) AS n;
        END LOOP;
END $$;

-- Заполнение заказов
DO $$
    DECLARE
        batch_size INTEGER := 50000;
        total_rows INTEGER := 6939490;
        total_batches INTEGER := 139;
        i INTEGER;
        rows_in_batch INTEGER;
        start_idx INTEGER;
        end_idx INTEGER;
    BEGIN
        FOR i IN 0..(total_batches - 1) LOOP
            start_idx := i * batch_size + 1;

            IF i = total_batches - 1 THEN
                rows_in_batch := total_rows - (i * batch_size);
            ELSE
                rows_in_batch := batch_size;
            END IF;

            end_idx := start_idx + rows_in_batch - 1;

            INSERT INTO "order" (customer_id, showtime_id, seat_id, order_time, price)
            SELECT
                ((n - 1) % 2500000) + 1 AS customer_id,
                ((n - 1) / 17) + 1 AS showtime_id,
                ((n - 1) % 150000) + 1 AS seat_id,
                CURRENT_TIMESTAMP - (random() * INTERVAL '200 days') AS order_time,
                (SELECT st.price
                 FROM seat s
                          JOIN seat_type st ON s.seat_type_id = st.id
                 WHERE s.id = ((n - 1) % 150000) + 1
                 LIMIT 1) AS price
            FROM generate_series(start_idx, end_idx) AS n
            WHERE ((n - 1) / 17) + 1 <= 400000
            ON CONFLICT (showtime_id, seat_id) DO NOTHING;
        END LOOP;
END $$;

ANALYZE;
