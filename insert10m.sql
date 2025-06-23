INSERT INTO seat_types(id, title)
SELECT
    gs.id,
    'type'||gs.id::TEXT
FROM generate_series(1, 4) AS gs(id);

---------------------------------------------------------------------
INSERT INTO halls(id, name, capacity, description)
SELECT
    gs.id,
    'hall'||gs.id::TEXT,
    400,
    random_sentence()
FROM generate_series(1, 5) AS gs(id);

---------------------------------------------------------------------
INSERT INTO seats(row_number, seat_number, seat_type_id, hall_id)
SELECT
    rs.id,
    ss.id,
    CASE 
        WHEN rs.id <= 5 THEN 1 
        WHEN rs.id <= 10 THEN 2 
        WHEN rs.id <= 15 THEN 3 
        ELSE 4 
    END,
    hs.id
FROM generate_series(1, 20) AS rs(id)
JOIN generate_series(1, 5) AS hs(id) ON true
JOIN generate_series(1, 20) AS ss(id) ON true
ORDER BY 4, 1, 2;

-----------------------------------------------------------------------
INSERT INTO movies(id, title, duration, genre, release_year)
SELECT
    gs.id,
    random_word() || ' ' || random_word(),
    (80 + random() * 100)::integer,
    random_word(),
    (2010 + random() * 15)::integer
FROM generate_series(1, 500) AS gs(id);

-----------------------------------------------------------------
INSERT INTO customers(id, name, email, phone, registration_date)
SELECT
    gs.id,
    random_word(),
    random_word() || round((111 + random() * 888))::text || '@mail.ru',
    round((89190000000 + random() * 9999999))::text,
    date_trunc('day', timestamp '2024-01-01' + random() * (timestamp '2024-12-31' - timestamp '2024-01-01'))
FROM generate_series(1, 3000000) AS gs(id);


-----------------------------------------------------------------
INSERT INTO screenings(hall_id, start_time, end_time, base_price, movie_id)
SELECT
    hs.id,
    timestamp '2020-03-01 09:00' + (3 * (ss.id - 1) + 9 * floor((ss.id - 1) / 5))::integer * interval '1 hour',
    timestamp '2020-03-01 09:00' + (3 * (ss.id - 1) + 9 * floor((ss.id - 1) / 5))::integer * interval '1 hour' + round(80 + random() * 100)::integer * interval '1 minute',
    round((1000 + random() * 1000))::integer,
    round(1 + random() * 499)::integer
FROM generate_series(1, 5) AS hs(id)
JOIN generate_series(1, 10000) AS ss(id) ON true;

-----------------------------------------------------------------
INSERT INTO pricing_rules(screening_id, seat_type_id, modifier)
SELECT
    ss.id,
    ts.id,
    (1 + random())::numeric(4,2)
FROM generate_series(1, 50000) AS ss(id)
JOIN generate_series(1, 4) AS ts(id) ON true;


-----------------------------------------------------------------
WITH cte AS (
    SELECT
        ss.id AS screening_id,
        ts.id AS tid,
        round((1 + random() * 2999999))::integer AS customer_id,
        timestamp '2020-01-01 09:00' + (2200 * random())::integer * interval '1 day' AS  purchase_date
    FROM generate_series(1, 50000) AS ss(id)
    JOIN generate_series(1, 200) AS ts(id) ON true
    ORDER BY 2
),
cte2 AS (
    SELECT 
        customer_id,
        screening_id,
        CASE
            WHEN random() < 0.5 THEN cte.tid + (s.hall_id - 1) * 400
            ELSE cte.tid + 200 + (s.hall_id - 1) * 400
        END AS seat_id,
        purchase_date
    FROM cte
    JOIN screenings s ON s.id = cte.screening_id
)
INSERT INTO tickets(customer_id, screening_id, seat_id, price, purchase_date)
SELECT 
    cte2.customer_id, 
    cte2.screening_id, 
    cte2.seat_id, 
    (s.base_price * pr.modifier)::smallint, 
    cte2.purchase_date
FROM cte2
LEFT JOIN screenings s ON s.id = cte2.screening_id
LEFT JOIN seats se ON se.id = cte2.seat_id
LEFT JOIN pricing_rules pr ON pr.screening_id = cte2.screening_id AND pr.seat_type_id = se.seat_type_id
ORDER BY 2, 3;