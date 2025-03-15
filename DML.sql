INSERT INTO public.films ("name")
VALUES ('Матрица'),
       ('Матрица: Перезагрузка '),
       ('Матрица: Революция'),
       ('Матрица: Воскрешение'),
       ('Рик и морти'),
       ('Рик и морти2');

INSERT INTO public.attribute_values (film_id, attibute_id, value_text, value_date, value_bool, value_float)
VALUES (1, 1, NULL, '1999-03-24 00:00:00', NULL, NULL),
       (1, 2, NULL, '1999-10-14 00:00:00', NULL, NULL),
       (1, 8, NULL, '1999-03-14 12:00:00', NULL, NULL),
       (2, 1, NULL, '2003-05-07 12:00:00', NULL, NULL),
       (2, 2, NULL, '2003-05-21 12:00:00', NULL, NULL),
       (2, 8, NULL, '2003-05-01 12:00:00', NULL, NULL),
       (3, 1, NULL, '2003-10-16 12:00:00', NULL, NULL),
       (3, 2, NULL, '2003-11-05 12:00:00', NULL, NULL),
       (3, 8, NULL, '2003-10-06 12:00:00', NULL, NULL),
       (4, 1, NULL, '2021-12-16 12:00:00', NULL, NULL);

INSERT INTO public.attribute_values (film_id, attibute_id, value_text, value_date, value_bool, value_float)
VALUES (4, 2, NULL, '2021-12-16 12:00:00', NULL, NULL),
       (4, 8, NULL, '2021-12-06 12:00:00', NULL, NULL),
       (1, 5, 'Текст рецензии критика', NULL, NULL, NULL),
       (2, 5, 'Текст рецензии критика 2', NULL, NULL, NULL),
       (5, 8, NULL, '2025-04-04 12:00:00', NULL, NULL),
       (7, 2, NULL, '2025-03-15 12:00:00', NULL, NULL),
       (7, 9, NULL, '2025-03-15 12:00:00', NULL, NULL),
       (7, 2, NULL, '2025-03-15 12:00:00', NULL, NULL);

INSERT INTO public.attribute_types ("type", "name")
VALUES ('timestamp', 'служебные даты'),
       ('text', 'рецензии'),
       ('bool', 'премия'),
       ('timestamp', 'важные даты');

INSERT INTO public."attributes" ("name", type_id)
VALUES ('Премьера в мире', 3),
       ('Премьера в РФ', 3),
       ('Оскар', 2),
       ('Ника', 2),
       ('Рецензии критиков', 1),
       ('Отзыв неизвестной киноакадемии', 1),
       ('Дата начала продажи билетов', 4),
       ('Когда запускать рекламу на ТВ', 4);
