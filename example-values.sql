TRUNCATE TABLE attribute_values, attributes, attribute_types, titles RESTART IDENTITY CASCADE;

INSERT INTO titles (title)
VALUES ('Интерстеллар'),
       ('Паразиты'),
       ('Дюна'),
       ('Начало');

INSERT INTO attribute_types (name)
VALUES ('Рецензии'),
       ('Премия'),
       ('Важные даты'),
       ('Служебные даты'),
       ('Рейтинг');

INSERT INTO attributes (type_id, name)
VALUES (1, 'Рецензия критика А'),
       (1, 'Отзыв неизвестной киноакадемии'),
       (2, 'Оскар'),
       (2, 'Ника'),
       (3, 'Мировая премьера'),
       (3, 'Премьера в РФ'),
       (4, 'Дата начала продаж билетов'),
       (4, 'Запуск рекламы на ТВ'),
       (5, 'IMDB рейтинг'),
       (5, 'Средняя касса');

INSERT INTO attribute_values (title_id, attribute_id, value_text)
VALUES (1, 1, 'Великолепный фильм о времени и любви'),
       (2, 2, 'Глубокий социальный подтекст');

INSERT INTO attribute_values (title_id, attribute_id, value_boolean)
VALUES (1, 3, TRUE),
       (2, 3, FALSE),
       (1, 4, TRUE);

INSERT INTO attribute_values (title_id, attribute_id, value_date)
VALUES (1, 5, '2014-10-26'),
       (1, 6, '2014-11-06'),
       (2, 5, '2019-05-21'),
       (2, 6, '2019-07-04'),
       (3, 7, '2025-03-01'),
       (4, 8, '2025-02-10');

INSERT INTO attribute_values (title_id, attribute_id, value_float)
VALUES (1, 9, 8.6),           -- IMDB рейтинг
       (2, 9, 8.5),
       (1, 10, 675000000.00), -- Средняя касса в долларах
       (2, 10, 258000000.00);
