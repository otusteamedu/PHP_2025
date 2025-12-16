-- EAV схема для кинотеатера
-- Создание таблиц

-- Таблица фильмов
CREATE TABLE films (
    film_id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    release_year INT
);

-- Таблица типов атрибутов
CREATE TABLE attribute_types (
    type_id SERIAL PRIMARY KEY,
    type_name VARCHAR(20) NOT NULL UNIQUE,
    description TEXT
);

-- Таблица атрибутов
CREATE TABLE attributes (
    attr_id SERIAL PRIMARY KEY,
    type_id INT NOT NULL REFERENCES attribute_types(type_id),
    attr_name VARCHAR(100) NOT NULL,
    display_name VARCHAR(200) NOT NULL
);

-- Таблица значений (EAV)
CREATE TABLE attribute_values (
    value_id SERIAL PRIMARY KEY,
    film_id INT NOT NULL REFERENCES films(film_id),
    attr_id INT NOT NULL REFERENCES attributes(attr_id),
    
    text_value TEXT,
    boolean_value BOOLEAN,
    date_value DATE,
    -- float_value на double_value для соответствия с PHP
    double_value DOUBLE PRECISION,
    
    -- Обновляем CHECK для нового поля
    CHECK (
        (text_value IS NOT NULL)::INTEGER + 
        (boolean_value IS NOT NULL)::INTEGER + 
        (date_value IS NOT NULL)::INTEGER + 
        (double_value IS NOT NULL)::INTEGER = 1
    )
);

-- Индексы
CREATE INDEX idx_attr_values_film ON attribute_values(film_id);
CREATE INDEX idx_attr_values_attr ON attribute_values(attr_id);
CREATE INDEX idx_attr_values_date ON attribute_values(date_value);

-- Функция для заполнения демо-данными
CREATE OR REPLACE FUNCTION fill_demo_data() 
RETURNS VOID AS $$
BEGIN
    -- Типы атрибутов
    INSERT INTO attribute_types (type_name, description) VALUES
    ('text', 'Текстовые значения'),
    ('boolean', 'Логические значения'),
    ('date', 'Даты'),
    ('double', 'Числовые значения'); -- double вместо float
    
    -- Атрибуты
    INSERT INTO attributes (type_id, attr_name, display_name) VALUES
    -- text
    (1, 'critic_review', 'Рецензия критиков'),
    (1, 'user_review', 'Отзыв зрителей'),
    -- boolean
    (2, 'oscar', 'Оскар'),
    (2, 'golden_globe', 'Золотой глобус'),
    (2, 'razzie', 'Золотая малина'),
    (2, 'nika', 'Ника'),
    (2, 'golden_eagle', 'Золотой орёл'),
    (2, 'saturn', 'Saturn'),
    -- dates
    (3, 'world_premiere', 'Мировая премьера'),
    (3, 'rf_premiere', 'Премьера в РФ'),
    (3, 'ticket_sale_start', 'Начало продажи билетов'),
    (3, 'tv_ad_start', 'Запуск ТВ-рекламы'),
    -- double, было float)
    (4, 'rating_imdb', 'Рейтинг IMDb'),
    (4, 'rating_kinopoisk', 'Рейтинг Кинопоиск');
    
    -- 10 фильмов
    INSERT INTO films (title, release_year) VALUES
    ('Титаник', 1997),
    ('Властелин колец: Братство кольца', 2001),
    ('Гарри Поттер и философский камень', 2001),
    ('Темный рыцарь', 2008),
    ('Форрест Гамп', 1994),
    ('Аватар', 2009),
    ('Шрек', 2001),
    ('Король Лев', 1994),
    ('Сияние', 1980),
    ('Звёздные войны: Новая надежда', 1977);
    
    -- Текстовые значения
    INSERT INTO attribute_values (film_id, attr_id, text_value) VALUES
    (1, 1, 'Эмоциональная и трагичная история любви'), (1, 2, 'Теплоход заехал в глыбу льда на фоне романтической истории любви'),
    (2, 1, 'Эпическое фэнтези-приключение'), (2, 2, 'Эльфы, тролли, люди помагают хоббитам, скинуть золотое кольцо в вулкан'),
    (3, 1, 'Магия и фантазия для детей'), (3, 2, 'Не только взрослые, но и даже дети любят этот фильм, особенно на новый год'),
    (4, 1, 'Шедевр супергеройского кино'), (4, 2, 'Лучший фильм о Бэтмене и точка'),
    (5, 1, 'Трогательная и мудрая история'), (5, 2, 'Невероятно вдохновляет купить катер и занятся ловлей креветок'),
    (6, 1, 'Великолепная визуализация'), (6, 2, 'Невероятно красивый мир и пришельцы, которые поклоняются деревьям'),
    (7, 1, 'Очень смешной мультфильм'), (7, 2, 'Лучший мультфильм про огра'),
    (8, 1, 'Трогательная анимация с музыкой'), (8, 2, 'История становления из львенка в царя приаяти'),
    (9, 1, 'Страх и напряжение'), (9, 2, 'Как можно сойти с ума в отеле и потом замерзуть, невероятно'),
    (10, 1, 'Классика космической фантастики'), (10, 2, 'Сын не признает отца и дружит с пришельцами');
    
    -- Boolean значения
    INSERT INTO attribute_values (film_id, attr_id, boolean_value) VALUES
    (1,3,true),(1,4,true),(1,5,false),(1,6,true),(1,7,false),(1,8,false),
    (2,3,true),(2,4,true),(2,5,false),(2,6,false),(2,7,false),(2,8,true),
    (3,3,false),(3,4,false),(3,5,false),(3,6,false),(3,7,false),(3,8,true),
    (4,3,true),(4,4,true),(4,5,false),(4,6,true),(4,7,false),(4,8,false),
    (5,3,true),(5,4,true),(5,5,false),(5,6,true),(5,7,false),(5,8,false),
    (6,3,false),(6,4,true),(6,5,false),(6,6,false),(6,7,false),(6,8,true),
    (7,3,false),(7,4,true),(7,5,false),(7,6,true),(7,7,false),(7,8,false),
    (8,3,true),(8,4,true),(8,5,false),(8,6,true),(8,7,false),(8,8,false),
    (9,3,false),(9,4,false),(9,5,false),(9,6,false),(9,7,false),(9,8,true),
    (10,3,false),(10,4,false),(10,5,false),(10,6,false),(10,7,false),(10,8,true);
    
    -- Значения даты
    INSERT INTO attribute_values (film_id, attr_id, date_value) VALUES
    (1,9,'1997-12-19'),(2,9,'2001-12-19'),(3,9,'2001-11-16'),(4,9,'2008-07-18'),
    (5,9,'1994-07-06'),(6,9,'2009-12-18'),(7,9,'2001-05-18'),(8,9,'1994-06-24'),
    (9,9,'1980-05-23'),(10,9,'1977-05-25'),
    (1,10,'1998-01-10'),(2,10,'2002-01-10'),(3,10,'2001-12-01'),(4,10,'2008-07-20'),
    (5,10,'1994-07-08'),(6,10,'2009-12-23'),(7,10,'2001-05-20'),(8,10,'1994-06-25'),
    (9,10,'1980-05-25'),(10,10,'1977-05-28'),
    (1,11,'1997-11-01'),(2,11,'2001-11-01'),(3,11,'2001-10-01'),(4,11,'2008-06-01'),
    (5,11,'1994-06-01'),(6,11,'2009-11-01'),(7,11,'2001-04-01'),(8,11,'1994-05-01'),
    (9,11,'1980-04-01'),(10,11,'1977-04-01'),
    (1,12,'1997-12-01'),(2,12,'2001-12-01'),(3,12,'2001-11-01'),(4,12,'2008-07-01'),
    (5,12,'1994-06-20'),(6,12,'2009-12-01'),(7,12,'2001-05-01'),(8,12,'1994-06-01'),
    (9,12,'1980-05-01'),(10,12,'1977-05-01');
    
    -- Рейтинги (double_value вместо float_value)
    INSERT INTO attribute_values (film_id, attr_id, double_value) VALUES
    -- rating_imdb (13)
    (1,13,7.8),(2,13,8.8),(3,13,7.6),(4,13,9.0),(5,13,8.8),
    (6,13,7.8),(7,13,7.9),(8,13,8.5),(9,13,8.4),(10,13,8.6),
    -- rating_kinopoisk (14)
    (1,14,8.0),(2,14,8.9),(3,14,7.8),(4,14,8.7),(5,14,8.9),
    (6,14,7.9),(7,14,8.1),(8,14,8.6),(9,14,8.1),(10,14,8.3);
    
    RAISE NOTICE 'Демо-данные успешно загружены!';
END;
$$ LANGUAGE plpgsql;

-- Создание представлений
CREATE OR REPLACE VIEW service_tasks AS
SELECT 
    f.title AS film,
    COALESCE(
        STRING_AGG(
            a.display_name, 
            ', '
        ) FILTER (WHERE av.date_value = CURRENT_DATE),
        'Нет задач'
    ) AS tasks_today,
    COALESCE(
        STRING_AGG(
            a.display_name, 
            ', '
        ) FILTER (WHERE av.date_value = CURRENT_DATE + INTERVAL '20 days'),
        'Нет задач'
    ) AS tasks_in_20_days
FROM films f
LEFT JOIN attribute_values av ON f.film_id = av.film_id
LEFT JOIN attributes a ON av.attr_id = a.attr_id
LEFT JOIN attribute_types at ON a.type_id = at.type_id
WHERE at.type_name = 'date'
    AND av.date_value IS NOT NULL
    AND av.date_value IN (CURRENT_DATE, CURRENT_DATE + INTERVAL '20 days')
GROUP BY f.film_id, f.title
ORDER BY f.title;

CREATE OR REPLACE VIEW marketing_data AS
SELECT 
    f.title AS film,
    at.type_name AS attribute_type,
    a.display_name AS attribute,
    CASE 
        WHEN av.text_value IS NOT NULL THEN av.text_value
        WHEN av.boolean_value = true THEN 'Да'
        WHEN av.boolean_value = false THEN 'Нет'
        WHEN av.date_value IS NOT NULL THEN to_char(av.date_value, 'DD.MM.YYYY')
        WHEN av.double_value IS NOT NULL THEN  -- double_value вместо float_value
            CASE 
                WHEN a.attr_name LIKE 'rating%' THEN ROUND(av.double_value::numeric, 1)::text
                ELSE ROUND(av.double_value::numeric, 2)::text
            END
        ELSE ''
    END AS value
FROM films f
JOIN attribute_values av ON f.film_id = av.film_id
JOIN attributes a ON av.attr_id = a.attr_id
JOIN attribute_types at ON a.type_id = at.type_id
ORDER BY f.title, at.type_name, a.display_name;

-- Функция для теста
CREATE OR REPLACE FUNCTION eav_schema() 
RETURNS TABLE (
    test_name TEXT,
    result TEXT,
    details TEXT
) AS $$
BEGIN
    RETURN QUERY
    
    SELECT 
        'Количество фильмов',
        COUNT(*)::TEXT,
        'Должно быть 10'
    FROM films
    HAVING COUNT(*) = 10
    
    UNION ALL
    
    SELECT 
        'Количество атрибутов',
        COUNT(*)::TEXT,
        'Должно быть 14 (2 text + 6 boolean + 4 date + 2 double)'  -- было 2 float
    FROM attributes
    HAVING COUNT(*) = 14
    
    UNION ALL
    
    SELECT 
        'Количество значений',
        COUNT(*)::TEXT,
        'Должно быть 140 (10 фильмов * 14 атрибутов)'
    FROM attribute_values
    HAVING COUNT(*) = 140
    
    UNION ALL
    
    SELECT 
        'Проверка целостности EAV',
        CASE 
            WHEN COUNT(*) = 0 THEN 'OK'
            ELSE 'ERROR: ' || COUNT(*)::TEXT || ' некорректных записей'
        END,
        'Все записи должны иметь одно значение'
    FROM attribute_values 
    WHERE text_value IS NULL 
        AND boolean_value IS NULL 
        AND date_value IS NULL 
        AND double_value IS NULL  -- double_value вместо float_value
    
    UNION ALL
    
    SELECT 
        'Представление marketing_data',
        COUNT(*)::TEXT || ' записей',
        'Успешно создано'
    FROM marketing_data
    HAVING COUNT(*) > 0
    
    UNION ALL
    
    SELECT 
        'Фильмы с Оскаром',
        STRING_AGG(title, ', '),
        'Оскар получили: ' || COUNT(*)::TEXT
    FROM (
        SELECT DISTINCT f.title
        FROM films f
        JOIN attribute_values av ON f.film_id = av.film_id
        JOIN attributes a ON av.attr_id = a.attr_id
        WHERE a.attr_name = 'oscar' AND av.boolean_value = true
    ) AS oscar_films
    GROUP BY (SELECT 1)
    
    UNION ALL
    
    SELECT 
        'Средний рейтинг IMDb',
        ROUND(AVG(av.double_value)::numeric, 2)::TEXT,  -- double_value вместо float_value
        'По всем фильмам'
    FROM attribute_values av
    JOIN attributes a ON av.attr_id = a.attr_id
    WHERE a.attr_name = 'rating_imdb';
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION add_test_tasks() 
RETURNS VOID AS $$
BEGIN
    UPDATE attribute_values 
    SET date_value = CURRENT_DATE
    WHERE attr_id IN (11, 12)
    AND film_id = 1;
    
    UPDATE attribute_values 
    SET date_value = CURRENT_DATE + INTERVAL '20 days'
    WHERE attr_id IN (11, 12)
    AND film_id = 2;
    
    RAISE NOTICE 'Тестовые задачи добавлены';
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION show_stats() 
RETURNS TABLE (
    metric TEXT,
    value TEXT
) AS $$
BEGIN
    RETURN QUERY
    
    SELECT 'Фильмы', COUNT(*)::TEXT FROM films
    UNION ALL
    SELECT 'Атрибуты', COUNT(*)::TEXT FROM attributes
    UNION ALL  
    SELECT 'Значения', COUNT(*)::TEXT FROM attribute_values
    UNION ALL
    SELECT 'Типы атрибутов', COUNT(*)::TEXT FROM attribute_types
    UNION ALL
    SELECT 'Фильмы с Оскаром', (
        SELECT COUNT(DISTINCT f.film_id)::TEXT
        FROM films f
        JOIN attribute_values av ON f.film_id = av.film_id
        JOIN attributes a ON av.attr_id = a.attr_id
        WHERE a.attr_name = 'oscar' AND av.boolean_value = true
    )
    UNION ALL
    SELECT 'Средний рейтинг IMDb', (
        SELECT ROUND(AVG(av.double_value)::numeric, 2)::TEXT  -- Добавил double_value
        FROM attribute_values av
        JOIN attributes a ON av.attr_id = a.attr_id
        WHERE a.attr_name = 'rating_imdb'
    );
END;
$$ LANGUAGE plpgsql;

-- Для проверки
-- Проверить количество фильмов
-- SELECT COUNT(*) as films_count FROM films;

-- Посмотреть несколько фильмов
-- SELECT * FROM films LIMIT 5;

-- Проверить значения атрибутов
-- SELECT * FROM attribute_values LIMIT 10;

-- Проверить представление
--SELECT * FROM marketing_data LIMIT 10;

-- Запустить тестовую функцию
-- SELECT * FROM eav_schema();

-- Посмотреть статистику
-- SELECT * FROM show_stats();

-- Проверить задачи
-- SELECT * FROM service_tasks;