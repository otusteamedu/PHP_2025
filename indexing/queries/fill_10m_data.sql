-- функция для генерации случайных строк
CREATE OR REPLACE FUNCTION random_string(length integer) RETURNS text AS $$
DECLARE
chars text[] := '{A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,0,1,2,3,4,5,6,7,8,9}';
    result text := '';
    i integer;
BEGIN
FOR i IN 1..length LOOP
        result := result || chars[1 + floor(random() * array_length(chars, 1))];
END LOOP;
RETURN result;
END;
$$ LANGUAGE plpgsql;

TRUNCATE TABLE ticket, customer, screening, seat, hall, cinema, movie, seat_type RESTART IDENTITY CASCADE;

INSERT INTO seat_type(name, price_modifier)
VALUES
    ('Обычное', 1.00),
    ('VIP', 1.50),
    ('Люкс', 2.00);

INSERT INTO cinema(name, address)
SELECT
    'Кинотеатр_' || random_string(8),
    'Адрес_' || random_string(15)
FROM generate_series(1, 50);

INSERT INTO hall(cinema_id, name, rows_count, seats_per_row)
SELECT
    c.cinema_id,
    'Зал_' || gs,
    15 + (gs % 5),
    20 + (gs % 5)
FROM cinema c, generate_series(1, 4) gs;

INSERT INTO seat(hall_id, row_number, seat_number, seat_type_id)
SELECT
    h.hall_id,
    rown,
    seatn,
    CASE
        WHEN (rown + seatn) % 10 < 6 THEN 1
        WHEN (rown + seatn) % 10 < 9 THEN 2
        ELSE 3
        END
FROM hall h
         CROSS JOIN generate_series(1, h.rows_count) AS rown
         CROSS JOIN generate_series(1, h.seats_per_row) AS seatn;

INSERT INTO movie(title, duration, age_rating)
SELECT
    'Фильм_' || gs || '_' || random_string(12),
    90 + (gs % 60),
    (ARRAY['0+','6+','12+','16+','18+'])[(gs % 5) + 1]
FROM generate_series(1, 5000) gs;

INSERT INTO customer(first_name, last_name, email, phone)
SELECT
    'Имя_' || gs,
    'Фамилия_' || gs,
    'user' || gs || '@test.com',
    '+7' || LPAD((9000000000::bigint + gs)::text, 10, '0')
FROM generate_series(1, 200000) gs;

INSERT INTO screening(movie_id, hall_id, show_date, show_time, base_price)
SELECT
    ((gs - 1) % 5000) + 1,
    ((gs - 1) % 200) + 1,
    (CURRENT_DATE - INTERVAL '365 days' + ((gs - 1) % 730) * INTERVAL '1 day')::date,
    ('08:00:00'::time + ((gs - 1) % 840) * INTERVAL '1 minute')::time,
    200 + ((gs - 1) % 400)
FROM generate_series(1, 150000) gs
ON CONFLICT (hall_id, show_date, show_time) DO NOTHING;

DO $$
DECLARE
screening_count INTEGER;
BEGIN
SELECT COUNT(*) INTO screening_count FROM screening;
RAISE NOTICE 'Создано сеансов: %', screening_count;
END $$;

-- делаем батчами по 100,000 для ускорения
DO $$
DECLARE
batch_size INTEGER := 100000;
    total_batches INTEGER := 85;
    i INTEGER;
    screening_count INTEGER;
    seat_count INTEGER;
    customer_count INTEGER;
BEGIN
SELECT COUNT(*) INTO screening_count FROM screening;
SELECT COUNT(*) INTO seat_count FROM seat;
SELECT COUNT(*) INTO customer_count FROM customer;

RAISE NOTICE 'Создание % билетов батчами по %', (batch_size * total_batches), batch_size;
    RAISE NOTICE 'Доступно: сеансов %, мест %, клиентов %', screening_count, seat_count, customer_count;

FOR i IN 1..total_batches LOOP
        INSERT INTO ticket(screening_id, seat_id, customer_id, price, purchase_time)
SELECT
    (((gs + i * batch_size - 1) * 7) % screening_count) + 1,
    (((gs + i * batch_size - 1) * 11) % seat_count) + 1,
    (((gs + i * batch_size - 1) * 13) % customer_count) + 1,
    250 + (((gs + i * batch_size - 1) * 17) % 350),
    (CURRENT_DATE - INTERVAL '365 days' +
    (((gs + i * batch_size - 1) * 19) % 730) * INTERVAL '1 day' +
    (((gs + i * batch_size - 1) * 23) % 1440) * INTERVAL '1 minute')::timestamp
FROM generate_series(1, batch_size) gs
ON CONFLICT (screening_id, seat_id) DO NOTHING;

RAISE NOTICE 'Завершен батч % из %', i, total_batches;
        IF i % 10 = 0 THEN
            COMMIT;
END IF;
END LOOP;
END $$;

ANALYZE;
