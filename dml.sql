insert into attribute_types
    (name)
values ('Рецензии'),
       ('Премия'),
       ('Рейтинг'),
       ('Служебные даты');

insert into attributes
    (type_id, name)
values (1, 'Рецензии критиков'),
       (2, 'Оскар'),
       (3, 'Рейтинг Кинопоиск'),
       (4, 'Дата начала продажи билетов'),
       (4, 'Дата актуальной задачи');

insert into films
    (name)
values ('Фильм 1'),
       ('Фильм 2'),
       ('Фильм 3'),
       ('Фильм 4');

insert into attribute_values (attribute_id, film_id, value_text, value_bool, value_number, value_date)
values
    -- Рецензии критиков
    (1, 1, 'Рецензия 1', null, null, null),
    (1, 2, 'Рецензия 2', null, null, null),
    (1, 3, 'Рецензия 3', null, null, null),
    (1, 4, 'Рецензия 4', null, null, null),

    -- Оскар
    (2, 1, , null, 6.1, null),
    (2, 2, null, null, 5.6, null),
    (2, 3, null, null, 8, null),
    (2, 4, null, null, 7, null),

    -- Рейтинг Кинопоиск
    (3, 1, null, true, null, null),
    (3, 2, null, true, null, null),
    (3, 3, null, true, null, null),
    (3, 4, null, true, null, null),

    -- Дата начала продажи билетов
    (4, 1, null, null, null, 'Fri Dec 10 1999 03:10:20 GMT+0000'),
    (4, 2, null, null, null, 'Mon Dec 01 2003 03:10:20 GMT+0000'),
    (4, 3, null, null, null, 'Wed Jul 06 1994 02:10:20 GMT+0000'),
    (4, 4, null, null, null, 'Fri Jul 19 2002 02:10:20 GMT+0000'),

    -- Дата актуальной задачи
    (5, 1, 'Задача 1', null, null, 'Wed Mar 12 2025 00:00:00 GMT+0000'),
    (5, 2, 'Задача 2', null, null, current_date),
    (5, 3, 'Задача 3', null, null, 'Mon Mar 31 2025 21:00:00 GMT+0000'),
    (5, 4, 'Задача 4', null, null, 'Sun Apr 12 2026 07:39:50 GMT+0000');