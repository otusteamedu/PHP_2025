-- Типы атрибутов
INSERT INTO attr_types (name, type, service) VALUES
('reviews', 'string', FALSE), -- 1
('awards', 'boolean', FALSE), -- 2
('important_dates', 'datetime', FALSE), -- 3
('service_dates', 'datetime', TRUE), -- 4
('prices', 'numeric', FALSE), -- 5
('descriptions', 'string', FALSE), -- 6
('durations', 'integer', FALSE); -- 7

-- Атрибуты
INSERT INTO attributes (name, attr_type_id) VALUES
('Рецензии критиков', 1), -- 1
('Отзыв неизвестной киноакадемии', 1); -- 2

INSERT INTO attributes (name, attr_type_id) VALUES
('Оскар', 2), -- 3
('Ника', 2); -- 4

INSERT INTO attributes (name, attr_type_id) VALUES
('Мировая премьера', 3), -- 5
('Премьера в РФ', 3); -- 6

INSERT INTO attributes (name, attr_type_id) VALUES
('Начало продажи билетов', 4), -- 7
('Запуск рекламы на ТВ', 4); -- 8

INSERT INTO attributes (name, attr_type_id) VALUES
('Цена', 5), -- 9
('Цена VIP', 5); -- 10

INSERT INTO attributes (name, attr_type_id) VALUES
('Описание фильма', 6), -- 11
('Резюме', 6); -- 12

INSERT INTO attributes (name, attr_type_id) VALUES
('Продолжительность (мин.)', 7); -- 13

-- Фильмы
INSERT INTO movies (title) VALUES
('Фильм 1'), -- 1
('Фильм 2'), -- 2
('Фильм 3'); -- 3

-- Фильм 1
INSERT INTO values (movie_id, attribute_id, string_val) VALUES
(1, 1, 'Бла бла бла 1'),
(1, 2, 'Бла бла бла 2');

INSERT INTO values (movie_id, attribute_id, boolean_val) VALUES
(1, 3, FALSE),
(1, 4, FALSE);

INSERT INTO values (movie_id, attribute_id, datetime_val) VALUES
(1, 5, '2024-10-26 00:00:00'),
(1, 6, '2024-11-06 00:00:00');

INSERT INTO values (movie_id, attribute_id, datetime_val) VALUES
(1, 7, '2024-11-07 09:00:00'),
(1, 8, '2024-11-01 10:00:00');

INSERT INTO values (movie_id, attribute_id, numeric_val) VALUES
(1, 9, 450.00),
(1, 10, 900.00);

-- Фильм 2
INSERT INTO values (movie_id, attribute_id, string_val) VALUES
(2, 1, 'Бла бла бла 3'),
(2, 2, 'Бла бла бла 4');

INSERT INTO values (movie_id, attribute_id, boolean_val) VALUES
(2, 3, TRUE),
(2, 4, TRUE);

INSERT INTO values (movie_id, attribute_id, datetime_val) VALUES
(2, 5, '2023-05-30 00:00:00'),
(2, 6, '2023-06-13 00:00:00');

INSERT INTO values (movie_id, attribute_id, datetime_val) VALUES
(2, 7, '2023-06-15 10:00:00'),
(2, 8, '2023-06-05 09:30:00');

INSERT INTO values (movie_id, attribute_id, numeric_val) VALUES
(2, 9, 400.00),
(2, 10, 800.00);

-- Фильм 3
INSERT INTO values (movie_id, attribute_id, string_val) VALUES
(3, 1, 'Бла бла бла 5'),
(3, 2, 'Бла бла бла 6');

INSERT INTO values (movie_id, attribute_id, boolean_val) VALUES
(3, 3, TRUE),
(3, 4, FALSE);

INSERT INTO values (movie_id, attribute_id, datetime_val) VALUES
(3, 5, '2025-12-18 00:00:00'),
(3, 6, '2025-12-25 00:00:00');

INSERT INTO values (movie_id, attribute_id, datetime_val) VALUES
(3, 7, '2025-12-26 08:00:00'),
(3, 8, '2025-12-19 09:00:00');

INSERT INTO values (movie_id, attribute_id, numeric_val) VALUES
(3, 9, 350.00),
(3, 10, 750.00);

-- Описание
INSERT INTO values (movie_id, attribute_id, string_val) VALUES
(1, 11, 'Бла бла бла 7'),
(2, 11, 'Бла бла бла 8'),
(3, 11, 'Бла бла бла 9');

-- Резюме
INSERT INTO values (movie_id, attribute_id, string_val) VALUES
(1, 12, 'Бла бла бла 10'),
(2, 12, 'Бла бла бла 11'),
(3, 12, 'Бла бла бла 12');

-- Продолжительность
INSERT INTO values (movie_id, attribute_id, integer_val) VALUES
(1, 13, 169),
(2, 13, 132),
(3, 13, 128);
