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
    value_boolean BOOLEAN
);

INSERT INTO movies (title, description) VALUES
('Movie A', 'description A'),
('Movie B', 'description B');

INSERT INTO attribute_types (type_name) VALUES
('Review'),
('Award'),
('Important Date'),
('Service Start Date');

INSERT INTO attributes (attribute_name, attribute_type_id) VALUES
('Critic Review', 1),
('Academy Review', 1),
('Oscar', 2),
('Nika', 2),
('World Premiere', 3),
('Russia Premiere', 3),
('Ticket Sale', 4),
('TV Advertising', 4);

INSERT INTO values (movie_id, attribute_id, value_text, value_date, value_boolean) VALUES
(1, 1, 'Great movie!', NULL, NULL),
(1, 2, 'Excellent movie!', NULL, NULL),
(1, 3, NULL, NULL, TRUE),
(1, 4, NULL, NULL, FALSE),
(1, 5, NULL, '2025-06-25', NULL),
(1, 6, NULL, '2025-06-30', NULL),
(1, 7, NULL, '2025-06-15', NULL),
(1, 8, NULL, '2025-06-05', NULL),
(2, 1, 'Not bad.', NULL, NULL),
(2, 2, 'Nonsense', NULL, NULL),
(2, 3, NULL, NULL, FALSE),
(2, 4, NULL, NULL, FALSE),
(2, 5, NULL, '2025-07-10', NULL),
(2, 6, NULL, '2025-07-15', NULL),
(2, 7, NULL, '2025-07-01', NULL),
(2, 8, NULL, '2025-06-15', NULL);
