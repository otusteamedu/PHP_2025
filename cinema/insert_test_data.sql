INSERT INTO halls (code, title) VALUES
('action_movies_hall', 'Зал боевиков'),
('love_movies_hall', 'Зал мелодрам'),
('comedy_movies_hall', 'Зал комедий');

INSERT INTO hall_line(hall_id, line_number, line_capacity) VALUES
((SELECT halls.id FROM halls WHERE id = 1 LIMIT 1), 1, 10),
((SELECT halls.id FROM halls WHERE id = 1 LIMIT 1), 2, 10),
((SELECT halls.id FROM halls WHERE id = 1 LIMIT 1), 3, 10);

INSERT INTO genres (code, title) VALUES ('action', 'Боевики'), ('love', 'Мелодрамы'), ('comedy', 'Комедии');

CALL create_clients(500000);
CALL create_films(1510000);
CALL create_screenings(1500);
CALL create_tickets(1500);
