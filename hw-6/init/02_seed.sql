INSERT INTO hall (name, seats_count) VALUES ('VIP', 12);
INSERT INTO hall (name, seats_count) VALUES ('STANDARD', 28);

INSERT INTO film (name, year, duration) VALUES ('Inception', 2010, 148);
INSERT INTO film (name, year, duration) VALUES ('Interstellar', 2014, 169);
INSERT INTO film (name, year, duration) VALUES ('The Matrix', 1999, 136);

INSERT INTO seance (film_id, hall_id, price, seance_time) VALUES (1, 1, 200.00, '2025-12-05 18:30');
INSERT INTO seance (film_id, hall_id, price, seance_time) VALUES (2, 2, 150.00, '2025-12-05 18:30');
INSERT INTO seance (film_id, hall_id, price, seance_time) VALUES (3, 1, 200.00, '2025-12-06 19:00');

INSERT INTO ticket (seance_id, row, seat, status) VALUES (1, 2, 3, 'sold', 200.00);
INSERT INTO ticket (seance_id, row, seat, status) VALUES (1, 2, 4, 'sold', 200.00);
INSERT INTO ticket (seance_id, row, seat, status) VALUES (2, 1, 1, 'sold', 150.00);
