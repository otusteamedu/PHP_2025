INSERT INTO film (name)
VALUES ('Inception'),
       ('Interstellar'),
       ('The Matrix');

INSERT INTO attribute_type (type_name)
VALUES
    ('text'),
    ('bool'),
    ('date'),
    ('float');

INSERT INTO attribute (attribute_type_id, name)
VALUES
    ( 1, 'Рецензии критиков'),
    (2, 'Оскар' ),
    (3, 'Мировая премьера'),
    (4, 'Рейтинг');

INSERT INTO value (film_id, attribute_id, value_text, value_bool, value_date, value_float)
VALUES
(1, 1, 'Фильм получил высокие оценки критиков', NULL, NULL, NULL),
(1, 2, NULL, TRUE, NULL, NULL),
(1, 3, NULL, NULL, '2010-07-16', NULL),
(1, 4, NULL, NULL, NULL, 8.8),

(2, 1, 'Невероятно масштабная космическая эпопея', NULL, NULL, NULL),
(2, 2, NULL, FALSE, NULL, NULL),
(2, 3, NULL, NULL, '2014-11-07', NULL),
(2, 4, NULL, NULL, NULL, 8.6),

(3, 1, 'Революционный фильм жанра sci-fi', NULL, NULL, NULL),
(3, 2, NULL, TRUE, NULL, NULL),
(3, 3, NULL, NULL, '1999-03-31', NULL),
(3, 4, NULL, NULL, NULL, 8.7);
