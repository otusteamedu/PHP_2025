SET search_path TO cinema_eav;
TRUNCATE TABLE attribute, attribute_type, attribute_value, movie RESTART IDENTITY CASCADE;

INSERT INTO attribute_type (
    type_name,
    data_type
) VALUES
    ('рецензии', 'text'),
    ('премия', 'boolean'),
    ('важные даты', 'date'),
    ('служебные даты', 'date'),
    ('продолжительность', 'integer'),
    ('рейтинг', 'float');

INSERT INTO attribute (
    attribute_name,
    attribute_type_id
) VALUES
    ('рецензии критиков', 1),
    ('отзыв неизвестной киноакадемии', 1),
    ('рецензии зрителей', 1),
    ('оскар', 2),
    ('ника', 2),
    ('мировая премьера', 3),
    ('премьера в РФ', 3),
    ('дата начала продажи билетов', 4),
    ('запуск рекламы на ТВ', 4),
    ('составление расписания сеансов', 4),
    ('длительность фильма', 5),
    ('рейтинг IMDb', 6),
    ('рейтинг Кинопоиск', 6);

INSERT INTO movie (
    name,
    year
) VALUES
    ('Матрица', 1999),
    ('Кин-дза-дза!', 1986),
    ('Аватар: Пламя и пепел', 2025),
    ('Дракула', 2025),
    ('Темный рыцарь', 2008);

-- Значения атрибутов для фильма "Матрица"
INSERT INTO attribute_value (
    movie_id,
    attribute_id,
    text_value,
    boolean_value,
    date_value,
    integer_value,
    float_value
) VALUES
    (1, 1, 'Тяжеловесной конструкции «Матрицы» явно не хватает коэновской элегантности.', NULL, NULL, NULL, NULL),
    (1, 3, 'Добро пожаловать в реальный мир!', NULL, NULL, NULL, NULL),
    (1, 3, 'Ищите в фильме смысл. Вы его увидите.', NULL, NULL, NULL, NULL),
    (1, 4, NULL, TRUE, NULL, NULL, NULL),
    (1, 6, NULL, NULL, '1999-03-24', NULL, NULL),
    (1, 7, NULL, NULL, '1999-10-14', NULL, NULL),
    (1, 8, NULL, NULL, current_date, NULL, NULL),
    (1, 11, NULL, NULL, NULL, 136, NULL),
    (1, 12, NULL, NULL, NULL, NULL, 8.7),
    (1, 13, NULL, NULL, NULL, NULL, 8.5);

-- Значения атрибутов для фильма "Кин-дза-дза!"
INSERT INTO attribute_value (
    movie_id,
    attribute_id,
    text_value,
    boolean_value,
    date_value,
    integer_value,
    float_value
) VALUES
    (2, 2, 'Великая, всеобъемлющая сатира.', NULL, NULL, NULL, NULL),
    (2, 5, NULL, TRUE, NULL, NULL, NULL),
    (2, 6, NULL, NULL, '1986-12-01', NULL, NULL),
    (2, 7, NULL, NULL, '1987-03-30', NULL, NULL),
    (2, 11, NULL, NULL, NULL, 135, NULL),
    (2, 12, NULL, NULL, NULL, NULL, 7.8),
    (2, 13, NULL, NULL, NULL, NULL, 8.1);

-- Значения атрибутов для фильма "Аватар: Пламя и пепел"
INSERT INTO attribute_value (
    movie_id,
    attribute_id,
    text_value,
    boolean_value,
    date_value,
    integer_value,
    float_value
) VALUES
    (3, 6, NULL, NULL, '2025-12-17', NULL, NULL),
    (3, 7, NULL, NULL, '2025-12-25', NULL, NULL),
    (3, 9, NULL, NULL, current_date + INTERVAL '20 days', NULL, NULL),
    (3, 11, NULL, NULL, NULL, 192, NULL);

-- Значения атрибутов для фильма "Дракула"
INSERT INTO attribute_value (
    movie_id,
    attribute_id,
    text_value,
    boolean_value,
    date_value,
    integer_value,
    float_value
) VALUES
    (4, 3, 'Новый «Дракула» от Люка Бессона. Зачем, мсье Бессон?', NULL, NULL, NULL, NULL),
    (4, 6, NULL, NULL, '2025-07-30', NULL, NULL),
    (4, 7, NULL, NULL, '2025-10-11', NULL, NULL),
    (4, 8, NULL, NULL, current_date + INTERVAL '20 days', NULL, NULL),
    (4, 9, NULL, NULL, current_date, NULL, NULL),
    (4, 10, NULL, NULL, current_date, NULL, NULL),
    (4, 11, NULL, NULL, NULL, 129, NULL),
    (4, 12, NULL, NULL, NULL, NULL, 6.1),
    (4, 13, NULL, NULL, NULL, NULL, 6.7);

-- Значения атрибутов для фильма "Темный рыцарь"
INSERT INTO attribute_value (
    movie_id,
    attribute_id,
    text_value,
    boolean_value,
    date_value,
    integer_value,
    float_value
) VALUES
    (5, 1, '«Темный рыцарь» — это ни в коем случае не кинокомикс, это эффектное и очень реалистичное, насколько это вообще возможно для фильма о Бэтмене кино.', NULL, NULL, NULL, NULL),
    (5, 2, 'Феноменальная игра Хита Леджера, неподражаемая харизма.', NULL, NULL, NULL, NULL),
    (5, 3, 'Один из лучших фильмов про Бэтмена.', NULL, NULL, NULL, NULL),
    (5, 4, NULL, TRUE, NULL, NULL, NULL),
    (5, 6, NULL, NULL, '2008-07-14', NULL, NULL),
    (5, 7, NULL, NULL, '2008-08-14', NULL, NULL),
    (5, 8, NULL, NULL, '2008-08-14', NULL, NULL),
    (5, 9, NULL, NULL, '2008-06-14', NULL, NULL),
    (5, 11, NULL, NULL, NULL, 152, NULL),
    (5, 12, NULL, NULL, NULL, NULL, 9.1),
    (5, 13, NULL, NULL, NULL, NULL, 8.5);
