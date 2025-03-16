INSERT INTO movies (title)
VALUES ('Чужой'),
       ('Чужие'),
       ('Чужой 3'),
       ('Чужой 4: Воскрешение'),
       ('Прометей'),
       ('Чужой: Ромул')
;

INSERT INTO attribute_types (name, data_type)
VALUES ('Рецензии', 'text'),
       ('Премии', 'boolean'),
       ('Важные даты', 'date'),
       ('Служебные даты', 'date')
;

INSERT INTO attributes (name, type_id)
VALUES ('Рецензия критиков', 1),
       ('Награда: Оскар', 2),
       ('Награда: BAFTA', 2),
       ('Мировая премьера', 3),
       ('Дата начала продаж', 4),
       ('Запуск рекламы на ТВ', 4)
;

INSERT INTO movie_attributes (movie_id, attribute_id, value_text, value_boolean, value_date)
VALUES
    -- Рецензии
    (1, 1, 'Фильм, положивший начало культовой франшизе.', null, null),
    (2, 1, 'Редкий сиквел, который не уступает оригиналу.', null, null),
    (5, 1, 'Амбициозная, но спорная предыстория серии.', null, null),

    -- Награды
    (1, 2, null, true, null),
    (2, 2, null, true, null),
    (2, 3, null, true, null),

    -- Важные даты (Мировая премьера)
    (1, 4, null, null, '1979-05-25'),
    (2, 4, null, null, '1986-07-18'),
    (3, 4, null, null, '1992-05-22'),
    (4, 4, null, null, '1997-11-12'),
    (5, 4, null, null, '2012-06-08'),
    (6, 4, null, null, '2025-08-15'),

    -- Служебные даты (продажи билетов, реклама)
    (1, 5, null, null, '1979-05-10'),
    (6, 5, null, null, '2025-07-01'),
    (3, 6, null, null, '1992-04-01'),
    (6, 6, null, null, '2025-06-10')
;
