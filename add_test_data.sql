-- Вставка данных о типах атрибутов
INSERT INTO "CINEMA".attribute_types (type_id, type_name, data_type, description, display_name, is_marketing, is_operational) VALUES
-- Основные типы из задания
(1, 'reviews', 'text', 'Текстовые рецензии и отзывы о фильме', 'Рецензии', TRUE, FALSE),
(2, 'awards', 'boolean', 'Награды и премии, полученные фильмом', 'Награды', TRUE, FALSE),
(3, 'important_dates', 'date', 'Важные даты, связанные с выходом и показом фильма', 'Важные даты', TRUE, FALSE),
(4, 'operational_dates', 'date', 'Служебные даты для планирования работы кинотеатра', 'Служебные даты', FALSE, TRUE);

-- Вставка данных об атрибутах
INSERT INTO "CINEMA".attributes (attribute_id, type_id, attribute_name, description, display_name) VALUES
-- Рецензии (type_id = 1)
(1, 1, 'critics_review', 'Рецензия критиков', 'Рецензия критиков'),
(2, 1, 'unknown_academy_review', 'Отзыв неизвестной киноакадемии', 'Отзыв киноакадемии'),

-- Награды (type_id = 2)
(3, 2, 'oscar', 'Получил Оскар', 'Оскар'),
(4, 2, 'nika', 'Получил Нику', 'Ника'),

-- Важные даты (type_id = 3)
(5, 3, 'world_premiere', 'Мировая премьера', 'Мировая премьера'),
(6, 3, 'russia_premiere', 'Премьера в РФ', 'Премьера в РФ'),

-- Служебные даты (type_id = 4)
(7, 4, 'ticket_sale_start', 'Дата начала продажи билетов', 'Начало продаж билетов'),
(8, 4, 'tv_ad_start', 'Когда запускать рекламу на ТВ', 'Запуск ТВ-рекламы');

-- Вставляем 50 фильмов с разными годами выпуска и продолжительностью
INSERT INTO "CINEMA".movies (title, original_title, release_year, duration)
SELECT
    'Фильм ' || i,
    'Movie ' || i,
    2000 + (i % 23), -- Годы от 2000 до 2022
    80 + (i % 120) -- Продолжительность от 80 до 200 минут
FROM generate_series(1, 50) AS i;

-- Вставляем 100 рецензий для случайных фильмов
INSERT INTO "CINEMA".attribute_values (movie_id, attribute_id, text_value)
SELECT
    (random() * 49 + 1)::int, -- Случайный movie_id от 1 до 50
    (random() * 1 + 1)::int, -- attribute_id 1 или 2 (рецензии)
    CASE
        WHEN random() > 0.5 THEN 'Отличный фильм! ' || substring(md5(random()::text), 1, 10)
        ELSE 'Плохой фильм. ' || substring(md5(random()::text), 1, 15)
        END
FROM generate_series(1, 100);

-- Вставляем 10 наград для случайных фильмов
INSERT INTO "CINEMA".attribute_values (movie_id, attribute_id, boolean_value)
SELECT
    (random() * 49 + 1)::int, -- Случайный movie_id от 1 до 50
    (random() * 1 + 3)::int, -- attribute_id 3 или 4 (награды)
    random() > 0.7 -- 30% вероятности получить награду
FROM generate_series(1, 10);

-- Вставляем 20 важных дат для случайных фильмов
INSERT INTO "CINEMA".attribute_values (movie_id, attribute_id, date_value)
SELECT
    (random() * 49 + 1)::int, -- Случайный movie_id от 1 до 50
    (random() * 1 + 5)::int, -- attribute_id 5 или 6 (важные даты)
    CURRENT_DATE - (random() * 365 * 5)::int -- Дата в пределах 5 лет от текущей
FROM generate_series(1, 20);

-- Вставляем служебные даты для 50 фильмов
INSERT INTO "CINEMA".attribute_values (movie_id, attribute_id, date_value)
SELECT
    i,
    7, -- Начало продажи билетов
    CURRENT_DATE + (i % 30) -- В пределах месяца от текущей даты
FROM generate_series(1, 50) AS i;

INSERT INTO "CINEMA".attribute_values (movie_id, attribute_id, date_value)
SELECT
    i,
    8, -- Запуск ТВ-рекламы
    CURRENT_DATE + (i % 20) - 5 -- В пределах 20 дней от текущей даты, некоторые уже прошли
FROM generate_series(1, 50) AS i;
