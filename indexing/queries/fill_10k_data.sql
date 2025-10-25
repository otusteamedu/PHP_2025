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
FROM generate_series(1, 5);

INSERT INTO hall(cinema_id, name, rows_count, seats_per_row)
SELECT
    c.cinema_id,
    'Зал_' || gs,
    10 + (gs % 5),
    15 + (gs % 5)
FROM cinema c, generate_series(1, 3) gs;

INSERT INTO seat(hall_id, row_number, seat_number, seat_type_id)
SELECT
    h.hall_id,
    rown,
    seatn,
    CASE
        WHEN (rown + seatn) % 10 < 7 THEN 1
        WHEN (rown + seatn) % 10 < 9 THEN 2
        ELSE 3
        END
FROM hall h
         CROSS JOIN generate_series(1, h.rows_count) AS rown
         CROSS JOIN generate_series(1, h.seats_per_row) AS seatn;

INSERT INTO movie(title, duration, age_rating)
SELECT
    'Фильм_' || gs || '_' || random_string(10),
    90 + (gs % 60),
    (ARRAY['0+','6+','12+','16+','18+'])[(gs % 5) + 1]
FROM generate_series(1, 200) gs;

INSERT INTO customer(first_name, last_name, email, phone)
SELECT
    'Имя_' || gs,
    'Фамилия_' || gs,
    'user' || gs || '@test.com',
    '+7' || LPAD((9000000000::bigint + gs)::text, 10, '0')
FROM generate_series(1, 1500) gs;

INSERT INTO screening(movie_id, hall_id, show_date, show_time, base_price)
SELECT
    ((gs - 1) % 200) + 1,
    ((gs - 1) % 15) + 1,
    (CURRENT_DATE - INTERVAL '30 days' + ((gs - 1) % 60) * INTERVAL '1 day')::date,
    ('08:00:00'::time + ((gs - 1) % 840) * INTERVAL '1 minute')::time,
    200 + ((gs - 1) % 300)
FROM generate_series(1, 1200) gs
ON CONFLICT (hall_id, show_date, show_time) DO NOTHING;

INSERT INTO ticket(screening_id, seat_id, customer_id, price, purchase_time)
SELECT
    ((gs - 1) % (SELECT COUNT(*) FROM screening)) + 1,
    ((gs - 1) % (SELECT COUNT(*) FROM seat)) + 1,
    ((gs - 1) % 1500) + 1,
    250 + ((gs - 1) % 250),
    (CURRENT_DATE - INTERVAL '30 days' +
    ((gs - 1) % 30) * INTERVAL '1 day' +
    ((gs - 1) % 1440) * INTERVAL '1 minute')::timestamp
FROM generate_series(1, 4000) gs
ON CONFLICT (screening_id, seat_id) DO NOTHING;
