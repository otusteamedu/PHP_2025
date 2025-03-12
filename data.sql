-- Вставка тестовых данных для кинотеатров
INSERT INTO cinema (name, location) VALUES
('Cinema 1', 'Location 1'),
('Cinema 2', 'Location 2');

-- Вставка тестовых данных для залов
INSERT INTO hall (cinema_id, name, capacity) VALUES
(1, 'Hall 1', 100),
(1, 'Hall 2', 150),
(2, 'Hall 1', 120);

-- Вставка тестовых данных для мест в зале
INSERT INTO seat (hall_id, row_number, seat_number) VALUES
(1, 1, 1), (1, 1, 2), (1, 1, 3), (1, 1, 4), (1, 1, 5),
(1, 2, 1), (1, 2, 2), (1, 2, 3), (1, 2, 4), (1, 2, 5),
(2, 1, 1), (2, 1, 2), (2, 1, 3), (2, 1, 4), (2, 1, 5),
(2, 2, 1), (2, 2, 2), (2, 2, 3), (2, 2, 4), (2, 2, 5);

-- Вставка тестовых данных для фильмов
INSERT INTO movie (title, duration) VALUES
('Movie A', 120),
('Movie B', 150),
('Movie C', 90);

-- Вставка тестовых данных для сеансов
INSERT INTO showtime (movie_id, hall_id, start_time) VALUES
 (1, 1, '2025-03-12 10:00:00'),
 (1, 2, '2025-03-12 14:00:00'),
 (2, 1, '2025-03-12 18:00:00'),
 (3, 2, '2025-03-12 20:00:00');

-- Вставка тестовых данных для категорий цен
INSERT INTO price_category (description, price) VALUES
('Standard', 10.00),
('VIP', 20.00),
('Student', 7.50);

-- Вставка тестовых данных для билетов
INSERT INTO ticket (showtime_id, seat_id, price_category_id, user_id) VALUES
(1, 1, 1, 1),
(1, 2, 2, 2),
(1, 3, 3, 3),
(1, 4, 1, 4),
(1, 5, 2, 5),
(2, 1, 1, 6),
(2, 2, 2, 7),
(2, 3, 3, 8),
(2, 4, 1, 9),
(2, 5, 2, 10),
(3, 1, 1, 11),
(3, 2, 2, 12),
(3, 3, 3, 13),
(4, 1, 1, 14),
(4, 2, 2, 15);
