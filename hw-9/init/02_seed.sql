INSERT INTO hall (name, seats_count)
VALUES
    ('VIP', 12),
    ('STANDARD', 28);

INSERT INTO film (name, year, duration)
SELECT
    'Film ' || gs,
    1989 + (random() * (2026 - 1989))::INT,
    80 + (random() * (220 - 80))::INT
FROM generate_series(1, 10000000) gs;

INSERT INTO seance (film_id, hall_id, price, seance_time)
SELECT
    gs,
    FLOOR(random() * 2)::INT + 1,
    20 + (random() * 20)::INT,
    NOW() + (random() * INTERVAL '30 days')
FROM generate_series(1, 10000000) gs;

INSERT INTO ticket (seance_id, row, seat, status, price)
SELECT s.id,
       r,
       c,
       CASE WHEN random() < 0.5 THEN 'sold' ELSE 'available' END,
       20 + (random() * 40)::INT
FROM seance s
         CROSS JOIN generate_series(1,10) r
         CROSS JOIN generate_series(1,10) c
WHERE s.id BETWEEN 1 AND 100000;