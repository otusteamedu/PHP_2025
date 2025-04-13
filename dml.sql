-- фильмы

INSERT INTO movies (title) VALUES 
('Интерстеллар'),
('Начало'),
('Паразиты'),
('Форма воды'),
('Брат');


-- типы атрибутов (attribute_types)
INSERT INTO attribute_types (name, data_type) VALUES
('рецензии', 'TEXT'),
('премия', 'BOOLEAN'),
('важные даты', 'DATE'),
('служебные даты', 'DATE'),
('количество мест', 'INT');

-- сами атрибуты (attributes)
-- TEXT
INSERT INTO attributes (name, attribute_type_id) VALUES
('рецензия критиков', 1),
('отзыв неизвестной киноакадемии', 1);

-- BOOLEAN
INSERT INTO attributes (name, attribute_type_id) VALUES
('оскар', 2),
('ника', 2);

-- DATE (важные даты)
INSERT INTO attributes (name, attribute_type_id) VALUES
('мировая премьера', 3),
('премьера в РФ', 3);

-- DATE (служебные даты)
INSERT INTO attributes (name, attribute_type_id) VALUES
('начало продаж билетов', 4),
('реклама на ТВ', 4);

-- INT
INSERT INTO attributes (name, attribute_type_id) VALUES
('количество мест', 5);



-- значения eav_values для разных фильмов
-- Интерстеллар
INSERT INTO eav_values (movie_id, attribute_id, value_string) VALUES
(1, 1, 'Великолепный научно-фантастический эпос'),
(1, 2, 'Непонятен, но красиво снят');

INSERT INTO eav_values (movie_id, attribute_id, value_bool) VALUES
(1, 3, true),
(1, 4, false);

INSERT INTO eav_values (movie_id, attribute_id, value_date) VALUES
(1, 5, '2014-11-05'),
(1, 6, '2014-11-06'),
(1, 7, '2014-10-01'),
(1, 8, '2014-09-20');

INSERT INTO eav_values (movie_id, attribute_id, value_int) VALUES
(1, 9, 250);

-- Начало
INSERT INTO eav_values (movie_id, attribute_id, value_string) VALUES
(2, 1, 'Ломает мозг, но мастерски снят'),
(2, 2, 'Фильм года по версии зрителей');

INSERT INTO eav_values (movie_id, attribute_id, value_bool) VALUES
(2, 3, false),
(2, 4, true);

INSERT INTO eav_values (movie_id, attribute_id, value_date) VALUES
(2, 5, '2010-07-08'),
(2, 6, '2010-09-16'),
(2, 7, '2010-06-20'),
(2, 8, '2010-06-01');

INSERT INTO eav_values (movie_id, attribute_id, value_int) VALUES
(2, 9, 300);

-- Паразиты
INSERT INTO eav_values (movie_id, attribute_id, value_string) VALUES
(3, 1, 'Социальный саспенс на высшем уровне'),
(3, 2, 'Неожиданно мощный финал');

INSERT INTO eav_values (movie_id, attribute_id, value_bool) VALUES
(3, 3, true),
(3, 4, true);

INSERT INTO eav_values (movie_id, attribute_id, value_date) VALUES
(3, 5, '2019-05-21'),
(3, 6, '2019-07-04'),
(3, 7, '2019-04-15'),
(3, 8, '2019-04-01');

INSERT INTO eav_values (movie_id, attribute_id, value_int) VALUES
(3, 9, 180);

-- Форма воды
INSERT INTO eav_values (movie_id, attribute_id, value_string) VALUES
(4, 1, 'Сказка для взрослых с визуальным стилем'),
(4, 2, 'Непривычный сюжет, но трогательно');

INSERT INTO eav_values (movie_id, attribute_id, value_bool) VALUES
(4, 3, true),
(4, 4, false);

INSERT INTO eav_values (movie_id, attribute_id, value_date) VALUES
(4, 5, '2017-08-31'),
(4, 6, '2018-01-18'),
(4, 7, '2017-08-01'),
(4, 8, '2017-07-20');

INSERT INTO eav_values (movie_id, attribute_id, value_int) VALUES
(4, 9, 150);

-- Брат
INSERT INTO eav_values (movie_id, attribute_id, value_string) VALUES
(5, 1, 'Культовое кино 90-х'),
(5, 2, 'Не хватает глубины, но искренне');

INSERT INTO eav_values (movie_id, attribute_id, value_bool) VALUES
(5, 3, false),
(5, 4, true);

INSERT INTO eav_values (movie_id, attribute_id, value_date) VALUES
(5, 5, '1997-10-17'),
(5, 6, '1997-11-01'),
(5, 7, '1997-09-20'),
(5, 8, '1997-09-01');

INSERT INTO eav_values (movie_id, attribute_id, value_int) VALUES
(5, 9, 200);


