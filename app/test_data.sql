-- Тестовые данные
INSERT INTO Movies (title, duration_minutes, genre) VALUES
('Чужой 3', 192, 'фантастика'),
('Голый пистолет', 103, 'комедия'),
('Неудержимые', 141, 'боевик');

-- Таблица Залы
INSERT INTO Halls (name, rows_count, seats_per_row) VALUES
('Зал 1', 10, 15),
('VIP зал', 5, 8);

-- Таблица Места
INSERT INTO Seats (hall_id, row_number, seat_number, seat_type) VALUES
(1, 1, 1, 'standard'), (1, 1, 2, 'standard'),
(2, 1, 1, 'vip'), (2, 1, 2, 'vip');

-- Таблица Сеансы
INSERT INTO Screenings (movie_id, hall_id, start_time, price) VALUES
(1, 1, '2025-11-26 19:00:00', 350.00),
(1, 2, '2025-11-25 21:00:00', 800.00);

-- Таблица Билеты
INSERT INTO Tickets (screening_id, seat_id, final_price) VALUES
(1, 1, 350.00), (1, 2, 350.00),
(2, 3, 800.00), (2, 4, 800.00);