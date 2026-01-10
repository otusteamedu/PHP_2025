INSERT INTO movies (name, release_date) VALUES
('Фильм 1', '2025-03-01'),
('Фильм 2', '2025-04-15');

INSERT INTO attribute_types (name, data_type) VALUES
('рецензии', 'TEXT'),
('премия', 'BOOLEAN'),
('важные даты', 'DATE'),
('служебные даты', 'DATE'),
('количество мест', 'INT');

INSERT INTO attributes (name, attribute_type_id) VALUES
('рецензия критиков', 1),
('Оскар', 2),
('мировая премьера', 3),
('дата начала продажи билетов', 4),
('количество мест в зале', 5);

INSERT INTO values (movie_id, attribute_id, value_text, value_boolean, value_date, value_int, value_float) VALUES
(1, 1, 'Отличный фильм!', NULL, NULL, NULL, NULL),
(1, 2, NULL, TRUE, NULL, NULL, NULL),
(1, 3, NULL, NULL, '2025-03-01', NULL, NULL),
(1, 4, NULL, NULL, '2025-02-20', NULL, NULL),
(2, 4, NULL, NULL, '2025-03-16', NULL, NULL),
(2, 4, NULL, NULL, '2025-04-05', NULL, NULL),
(1, 5, NULL, NULL, NULL, 100, NULL),
(2, 5, NULL, NULL, NULL, 150, NULL);
