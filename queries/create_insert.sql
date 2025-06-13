CREATE TABLE movies (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL
);

CREATE TABLE attribute_types (
    id SERIAL PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL
);

CREATE TABLE attributes (
    id SERIAL PRIMARY KEY,
    attribute_name VARCHAR(255) NOT NULL,
    attribute_type_id INT REFERENCES attribute_types(id)
);

CREATE TABLE values (
    id SERIAL PRIMARY KEY,
    movie_id INT REFERENCES movies(id),
    attribute_id INT REFERENCES attributes(id),
    value_text TEXT,
    value_date DATE,
    value_boolean BOOLEAN,
    value_integer INTEGER,
    value_real REAL,
    value_decimal DECIMAL(10,2)
);

INSERT INTO movies (title, description) VALUES
('Movie A', 'description A'),
('Movie B', 'description B'),
('Movie C', 'description C');

INSERT INTO attribute_types (type_name) VALUES
('Review'),
('Award'),
('Important Date'),
('Service Start Date'),
('Rating'),
('Box Office $'),
('Spectators In Millions');

INSERT INTO attributes (attribute_name, attribute_type_id) VALUES
('Critic Review', 1),
('Academy Review', 1),
('Oscar', 2),
('Nika', 2),
('World Premiere', 3),
('Russia Premiere', 3),
('Ticket Sale', 4),
('TV Advertising', 4),
('Kinopoisk Rating', 5),
('Imdb Rating', 5),
('Worldwide Box Office $', 6),
('Worldwide Spectators', 7);

INSERT INTO values (movie_id, attribute_id, value_text, value_date, value_boolean, value_integer, value_real, value_decimal) VALUES
(1, 1, 'Great movie!', NULL, NULL, NULL, NULL, NULL),
(1, 2, 'Excellent movie!', NULL, NULL, NULL, NULL, NULL),
(1, 3, NULL, NULL, TRUE, NULL, NULL, NULL),
(1, 4, NULL, NULL, FALSE, NULL, NULL, NULL),
(1, 5, NULL, '2025-06-25', NULL, NULL, NULL, NULL),
(1, 6, NULL, '2025-06-30', NULL, NULL, NULL, NULL),
(1, 7, NULL, '2025-06-15', NULL, NULL, NULL, NULL),
(1, 8, NULL, '2025-06-05', NULL, NULL, NULL, NULL),
(1, 9, NULL, NULL, NULL, NULL, 8.20, NULL),
(1, 10, NULL, NULL, NULL, NULL, 7.50, NULL),
(1, 11, NULL, NULL, NULL, 215000000, NULL, NULL),
(1, 12, NULL, NULL, NULL, NULL, 120.254, NULL),
(2, 1, 'Not bad.', NULL, NULL, NULL, NULL, NULL),
(2, 2, 'Nonsense', NULL, NULL, NULL, NULL, NULL),
(2, 3, NULL, NULL, FALSE, NULL, NULL, NULL),
(2, 4, NULL, NULL, FALSE, NULL, NULL, NULL),
(2, 5, NULL, '2025-07-15', NULL, NULL, NULL, NULL),
(2, 6, NULL, '2025-07-20', NULL, NULL, NULL, NULL),
(2, 7, NULL, '2025-07-05', NULL, NULL, NULL, NULL),
(2, 8, NULL, '2025-06-20', NULL, NULL, NULL, NULL),
(2, 9, NULL, NULL, NULL, NULL, 7.90, NULL),
(2, 10, NULL, NULL, NULL, NULL, 7.60, NULL),
(2, 11, NULL, NULL, NULL, 152000000, NULL, NULL),
(2, 12, NULL, NULL, NULL, NULL, 57.126, NULL),
(3, 1, 'Very nice', NULL, NULL, NULL, NULL, NULL),
(3, 2, 'Not bad', NULL, NULL, NULL, NULL, NULL),
(3, 3, NULL, NULL, FALSE, NULL, NULL, NULL),
(3, 4, NULL, NULL, FALSE, NULL, NULL, NULL),
(3, 5, NULL, '2025-07-10', NULL, NULL, NULL, NULL),
(3, 6, NULL, '2025-07-15', NULL, NULL, NULL, NULL),
(3, 7, NULL, '2025-07-01', NULL, NULL, NULL, NULL),
(3, 8, NULL, '2025-06-15', NULL, NULL, NULL, NULL),
(3, 9, NULL, NULL, NULL, NULL, 7.10, NULL),
(3, 10, NULL, NULL, NULL, NULL, 7.00, NULL),
(3, 11, NULL, NULL, NULL, 133000000, NULL, NULL),
(3, 12, NULL, NULL, NULL, NULL, 69.452, NULL);
