INSERT INTO attribute_type (name, data_type)
VALUES
    ('Рецензии', 'text'),
    ('Награды', 'boolean'),
    ('Важные даты', 'date'),
    ('Служебные даты', 'date'),
    ('Рейтинг', 'decimal'),
    ('Статистика', 'integer');

INSERT INTO movie (title)
VALUES
    ('Интерстеллар'),
    ('Оппенгеймер'),
    ('Дюна 2'),
    ('28 лет спустя');

INSERT INTO attribute (name, attribute_type_id)
VALUES
    ('Рецензия критика', 1),
    ('Рецензия академии', 1),
    ('Оскар', 2),
    ('Ника', 2),
    ('Золотой глобус', 2),
    ('Мировая премьера', 3),
    ('Премьера в РФ', 3),
    ('Начало продажи билетов', 4),
    ('Запуск рекламы ТВ', 4),
    ('Рейтинг IMDb', 5),
    ('Рейтинг Кинопоиск', 5),
    ('Проданные билеты', 6);

INSERT INTO attribute_value (movie_id, attribute_id, boolean_value, text_value, date_value, integer_value, decimal_value)
VALUES
    (1, 3, true, NULL, NULL, NULL,NULL),
    (1, 1, NULL, 'Шедевр Нолана!', NULL, NULL, NULL),
    (1, 7, NULL, NULL, '2026-01-02', NULL, NULL),
    (1, 8, NULL, NULL, '2026-01-02', NULL, NULL),
    (1, 9, NULL, NULL, '2025-12-13', NULL, NULL),
    (1, 10, NULL, NULL, NULL, NULL, 9.0),
    (1, 11, NULL, NULL, NULL, NULL, 8.7),
    (1, 12, NULL, NULL, NULL, 5003045, NULL);

INSERT INTO attribute_value (movie_id, attribute_id, boolean_value, text_value, date_value, integer_value, decimal_value)
VALUES
    (2, 3, true, NULL, NULL, NULL, NULL),
    (2, 4, true, NULL, NULL, NULL, NULL),
    (2, 2, NULL, 'Лучший фильм года!', NULL, NULL, NULL),
    (2, 9, NULL, NULL, '2025-12-13', NULL, NULL),
    (2, 11, NULL, NULL, NULL, NULL, 8.4);

INSERT INTO attribute_value (movie_id, attribute_id, boolean_value, text_value, date_value, integer_value, decimal_value)
VALUES
    (3, 5, false, NULL, NULL, NULL, NULL),
    (3, 1, NULL, 'Визуальный шедевр', NULL, NULL, NULL),
    (3, 10, NULL, NULL, NULL, NULL, 8.3),
    (3, 12, NULL, NULL, NULL, 1250067, NULL);

INSERT INTO attribute_value (movie_id, attribute_id, boolean_value, text_value, date_value, integer_value, decimal_value)
VALUES
    (4, 4, true, NULL, NULL, NULL, NULL),
    (4, 2, NULL, 'Феномен года', NULL, NULL, NULL);
