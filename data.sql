TRUNCATE film,
atr_film,
atr_type,
atr_value CASCADE;

INSERT INTO
    film (name)
VALUES
    ('Старикам тут не место'),
    ('Гладиатор'),
    ('Географ глобус пропил'),
    ('Мастер и Маргарита'),
    ('Шрек');

INSERT INTO
    atr_type (type_name, base_type)
VALUES
    ('длительность', 'INT'),
    ('рецензии', 'TEXT'),
    ('рейтинг', 'DECIMAL'),
    ('важные даты', 'DATE'),
    ('служебные даты', 'DATE'),
    ('премия', 'BOOLEAN');

INSERT INTO
    atr_film (name, id_atr_type)
VALUES
    ('длительность в минутах', 1),
    ('рецензии критиков', 2),
    ('отзыв', 2),
    ('рейтинг', 3),
    ('мировая премьера', 4),
    ('премьера в РФ', 4),
    ('запуск рекламы', 5),
    ('старт продажи билетов', 5),
    ('оскар', 6),
    ('ника', 6);

INSERT INTO
    atr_value (
        id_film,
        id_atr_film,
        int_value,
        text_value,
        decimal_value,
        date_value,
        boolean_value
    )
VALUES
    -- 'Старикам тут не место'
    (1, 1, 122, NULL, NULL, NULL, NULL),
    (
        1,
        2,
        NULL,
        'мрачный, нетрадиционный триллер',
        NULL,
        NULL,
        NULL
    ),
    (
        1,
        3,
        NULL,
        'потрясающий фильм!',
        NULL,
        NULL,
        NULL
    ),
    (1, 4, NULL, NULL, 7.8, NULL, NULL),
    (1, 5, NULL, NULL, NULL, '2025-09-30', NULL),
    (1, 6, NULL, NULL, NULL, '2025-09-14', NULL),
    (1, 7, NULL, NULL, NULL, '2025-09-13', NULL),
    (1, 8, NULL, NULL, NULL, '2025-09-15', NULL),
    (1, 9, NULL, NULL, NULL, NULL, TRUE),
    (1, 10, NULL, NULL, NULL, NULL, FALSE);

INSERT INTO
    atr_value (
        id_film,
        id_atr_film,
        int_value,
        text_value,
        decimal_value,
        date_value,
        boolean_value
    )
VALUES
    -- 'Гладиатор'
    (2, 1, 155, NULL, NULL, NULL, NULL),
    (
        2,
        2,
        NULL,
        'Насыщенный экстракт тестостерона',
        NULL,
        NULL,
        NULL
    ),
    (
        2,
        3,
        NULL,
        'фильм бессмысленный',
        NULL,
        NULL,
        NULL
    ),
    (2, 4, NULL, NULL, 8.6, NULL, NULL),
    (2, 5, NULL, NULL, NULL, '2025-10-09', NULL),
    (2, 6, NULL, NULL, NULL, '2025-10-15', NULL),
    (2, 7, NULL, NULL, NULL, '2025-10-12', NULL),
    (2, 8, NULL, NULL, NULL, '2025-10-30', NULL),
    (2, 9, NULL, NULL, NULL, NULL, TRUE),
    (2, 10, NULL, NULL, NULL, NULL, FALSE);

INSERT INTO
    atr_value (
        id_film,
        id_atr_film,
        int_value,
        text_value,
        decimal_value,
        date_value,
        boolean_value
    )
VALUES
    -- ''Географ глобус пропил''
    (3, 1, 125, NULL, NULL, NULL, NULL),
    (
        3,
        2,
        NULL,
        'Не нужны нам такие герои!',
        NULL,
        NULL,
        NULL
    ),
    (
        3,
        3,
        NULL,
        'Тупиковый учительский подвиг',
        NULL,
        NULL,
        NULL
    ),
    (3, 4, NULL, NULL, 7.4, NULL, NULL),
    (3, 5, NULL, NULL, NULL, NULL, NULL),
    (3, 6, NULL, NULL, NULL, '2025-11-07', NULL),
    (3, 7, NULL, NULL, NULL, '2025-10-10', NULL),
    (3, 8, NULL, NULL, NULL, '2025-11-06', NULL),
    (3, 9, NULL, NULL, NULL, NULL, FALSE),
    (3, 10, NULL, NULL, NULL, NULL, TRUE);

INSERT INTO
    atr_value (
        id_film,
        id_atr_film,
        int_value,
        text_value,
        decimal_value,
        date_value,
        boolean_value
    )
VALUES
    -- 'Мастер и Маргарита'
    (4, 1, 157, NULL, NULL, NULL, NULL),
    (
        4,
        2,
        NULL,
        'Экранизация одного из самых популярных романов XX века',
        NULL,
        NULL,
        NULL
    ),
    (
        4,
        3,
        NULL,
        'Смешанные впечатления от фильма',
        NULL,
        NULL,
        NULL
    ),
    (4, 4, NULL, NULL, 7.4, NULL, NULL),
    (4, 5, NULL, NULL, NULL, '2025-09-25', NULL),
    (4, 6, NULL, NULL, NULL, '2025-09-25', NULL),
    (4, 7, NULL, NULL, NULL, '2025-09-02', NULL),
    (4, 8, NULL, NULL, NULL, '2025-09-24', NULL),
    (4, 9, NULL, NULL, NULL, NULL, FALSE),
    (4, 10, NULL, NULL, NULL, NULL, TRUE);

INSERT INTO
    atr_value (
        id_film,
        id_atr_film,
        int_value,
        text_value,
        decimal_value,
        date_value,
        boolean_value
    )
VALUES
    -- 'Шрек'
    (5, 1, 90, NULL, NULL, NULL, NULL),
    (
        5,
        2,
        NULL,
        'Он делает счастливым и младенца и профессора',
        NULL,
        NULL,
        NULL
    ),
    (
        5,
        3,
        NULL,
        'Приключения троля и осла',
        NULL,
        NULL,
        NULL
    ),
    (5, 4, NULL, NULL, 8.2, NULL, NULL),
    (5, 5, NULL, NULL, NULL, '2025-04-22', NULL),
    (5, 6, NULL, NULL, NULL, '2025-10-12', NULL),
    (5, 7, NULL, NULL, NULL, '2025-02-09', NULL),
    (5, 8, NULL, NULL, NULL, '2025-04-21', NULL),
    (5, 9, NULL, NULL, NULL, NULL, TRUE),
    (5, 10, NULL, NULL, NULL, NULL, FALSE);