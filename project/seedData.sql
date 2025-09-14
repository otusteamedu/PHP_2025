-- Заполнение для 10 000 строк (малый набор)
-- Очистка таблиц перед началом (если нужно)
TRUNCATE TABLE Tickets, TicketPrices, Sessions, Seats, Movies, Halls CASCADE;

BEGIN;

-- Halls (50 halls)
INSERT INTO Halls (name)
SELECT 'Hall ' || g FROM generate_series(1, 50) g;

-- Movies (1000 movies)
INSERT INTO Movies (title, duration)
SELECT 'Movie ' || g, 90 + (g % 60) FROM generate_series(1, 1000) g;

-- Seats (50 halls * 200 seats = 10 000)
INSERT INTO Seats (hall_id, row_number, seat_number, seat_type)
SELECT h.hall_id, r, s, CASE WHEN random() < 0.5 THEN 'standard' ELSE 'vip' END
FROM Halls h, generate_series(1, 10) r, generate_series(1, 20) s;

-- Sessions (100 000 sessions)
-- Используем подзапрос для выбора существующих movie_id из таблицы Movies
INSERT INTO Sessions (movie_id, hall_id, start_time)
SELECT
    (SELECT movie_id FROM Movies ORDER BY random() LIMIT 1), -- Случайный movie_id из Movies
    (SELECT hall_id FROM Halls ORDER BY random() LIMIT 1),  -- Случайный hall_id из Halls
    now() - interval '1 year' * random() + interval '1 hour' * g
FROM generate_series(1, 100000) g;

-- TicketPrices (2 per session * 100k = 200 000)
INSERT INTO TicketPrices (session_id, seat_type, price)
SELECT session_id, 'standard', 10 + random() * 20 FROM Sessions;
INSERT INTO TicketPrices (session_id, seat_type, price)
SELECT session_id, 'vip', 20 + random() * 30 FROM Sessions;

-- Tickets (~9 700 000 tickets, batch insert with unique check)
DO $$
BEGIN
FOR i IN 1..100 LOOP
    INSERT INTO Tickets (session_id, seat_id, price, purchase_time)
SELECT DISTINCT ON (s.session_id, se.seat_id) -- Гарантируем уникальность
    s.session_id, se.seat_id, tp.price, s.start_time - interval '1 day' * random()
FROM Sessions s
    JOIN Seats se ON se.hall_id = s.hall_id
    JOIN TicketPrices tp ON tp.session_id = s.session_id AND tp.seat_type = se.seat_type
WHERE s.session_id % 100 = i % 100 AND random() < 0.5
    LIMIT 97000;
END LOOP;
END $$;

COMMIT;


-- Заполнение для 10 000 000 строк
-- Очистка таблиц перед началом (если нужно)
TRUNCATE TABLE Tickets, TicketPrices, Sessions, Seats, Movies, Halls CASCADE;

BEGIN;

-- Halls (50 halls)
INSERT INTO Halls (name)
SELECT 'Hall ' || g FROM generate_series(1, 50) g;

-- Movies (1000 movies)
INSERT INTO Movies (title, duration)
SELECT 'Movie ' || g, 90 + (g % 60) FROM generate_series(1, 1000) g;

-- Seats (50 halls * 200 seats = 10 000)
INSERT INTO Seats (hall_id, row_number, seat_number, seat_type)
SELECT h.hall_id, r, s, CASE WHEN random() < 0.5 THEN 'standard' ELSE 'vip' END
FROM Halls h, generate_series(1, 10) r, generate_series(1, 20) s;

-- Sessions (100 000 sessions)
-- Используем подзапросы для выбора существующих movie_id и hall_id
INSERT INTO Sessions (movie_id, hall_id, start_time)
SELECT
    (SELECT movie_id FROM Movies ORDER BY random() LIMIT 1), -- Случайный movie_id из Movies
    (SELECT hall_id FROM Halls ORDER BY random() LIMIT 1),  -- Случайный hall_id из Halls
    now() - interval '1 year' * random() + interval '1 hour' * g
FROM generate_series(1, 100000) g;

-- TicketPrices (2 per session * 100k = 200 000)
INSERT INTO TicketPrices (session_id, seat_type, price)
SELECT session_id, 'standard', 10 + random() * 20 FROM Sessions;
INSERT INTO TicketPrices (session_id, seat_type, price)
SELECT session_id, 'vip', 20 + random() * 30 FROM Sessions;

-- Tickets (~9 700 000 tickets, batch insert)
DO $$
BEGIN
FOR i IN 1..100 LOOP  -- Batch to avoid memory issues
    INSERT INTO Tickets (session_id, seat_id, price, purchase_time)
SELECT DISTINCT ON (s.session_id, se.seat_id) -- Гарантируем уникальность
    s.session_id, se.seat_id, tp.price, s.start_time - interval '1 day' * random()
FROM Sessions s
    JOIN Seats se ON se.hall_id = s.hall_id
    JOIN TicketPrices tp ON tp.session_id = s.session_id AND tp.seat_type = se.seat_type
WHERE s.session_id % 100 = i % 100 AND random() < 0.5  -- ~50% occupancy per batch
    LIMIT 97000;  -- Per batch
END LOOP;
END $$;

COMMIT;