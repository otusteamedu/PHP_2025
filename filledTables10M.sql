INSERT INTO Hall (capacity)
SELECT (RANDOM() * 100 + 50)::INT
FROM generate_series(1, 100000);


INSERT INTO Movie (title, duration, genre)
SELECT 
    'Movie ' || gs, 
    (RANDOM() * 120 + 60)::INT,
    CASE 
        WHEN gs % 3 = 0 THEN 'Action'
        WHEN gs % 3 = 1 THEN 'Comedy'
        ELSE 'Drama' 
    END
FROM generate_series(1, 2000000) AS gs;



INSERT INTO Session (hall_id, movie_id, start_time, end_time, base_price)
SELECT 
    (RANDOM() * 99999 + 1)::INT,
    (RANDOM() * 1999999 + 1)::INT,
    start_time,
    start_time + INTERVAL '2 hours' AS end_time,
    ROUND((RANDOM() * 10 + 5)::numeric, 2)
FROM (
    SELECT DATE '2025-08-01' + (RANDOM() * INTERVAL '60 days') AS start_time
    FROM generate_series(1, 4000000)
) AS random_time;




INSERT INTO SeatTypes (title)
VALUES ('Regular'), ('VIP'), ('Student'), ('Senior');


INSERT INTO Seats (hall_id, row_number, seat_number, seat_type_id)
SELECT 
    (RANDOM() * 99999 + 1)::INT AS hall_id,
    (RANDOM() * 10 + 1)::INT AS row_number, 
    (RANDOM() * 20 + 1)::INT AS seat_number,  
    (RANDOM() * 3 + 1)::INT AS seat_type_id  
FROM generate_series(1, 1000000);


INSERT INTO Client (name, email)
SELECT 
    'Client ' || gs,
    'client' || gs || '@example.com'
FROM generate_series(1, 2000000) AS gs;


INSERT INTO Ticket (session_id, client_id, seat_id, price)
SELECT 
    (RANDOM() * 3999999 + 1)::INT,   
    (RANDOM() * 1999999 + 1)::INT,
    (RANDOM() * 999999 + 1)::INT, 
    ROUND((RANDOM() * 10 + 5)::numeric, 2) 
FROM generate_series(1, 2000000);


INSERT INTO PricingRules (session_id, seat_type_id, modifier)
SELECT 
    (RANDOM() * 3999999 + 1)::INT,
    (RANDOM() * 3 + 1)::INT,
    ROUND((RANDOM() * 5 - 2.5)::numeric, 2)
FROM generate_series(1, 100000);