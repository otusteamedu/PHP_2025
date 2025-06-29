INSERT INTO attribute_types (type_name) VALUES
('review'),
('award'),
('important_date'),
('service_date'),
('score');

INSERT INTO attributes (attribute_name, type_id) VALUES
('critic_review', 1),
('oscar', 2),
('world_premiere', 3),
('ticket_sale_start', 4),
('rating', 5),
('creators_number', 5);

INSERT INTO films (title, release_date) VALUES
('Superman', '2025-07-01'),
('Fantastic four', '2025-07-15');

INSERT INTO values (film_id, attribute_id, value_text, value_date, value_boolean, value_float, value_int) VALUES
(1, 1, 'Great movie!', NULL, NULL, NULL, NULL),
(1, 2, NULL, NULL, TRUE, NULL, NULL), 
(1, 3, NULL, '2025-06-30', NULL, NULL, NULL),
(1, 4, NULL, '2025-06-15', NULL, NULL, NULL),
(1, 5, NULL, NULL, NULL, 8.1, NULL),
(1, 6, NULL, NULL, NULL, NULL, 15);     
