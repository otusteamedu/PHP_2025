-- Генерация тестовых данных для БД кинотеатра
-- p_target_rows — целевое количество записей в БД
-- ИСПОЛЬЗОВАНИЕ:
-- CALL populate_cinema_data(10000);      -- ~10,000 записей (маленькая БД)
-- CALL populate_cinema_data(10000000);   -- ~10,000,000 записей (большая БД)

CREATE OR REPLACE PROCEDURE populate_cinema_data(p_target_rows INT DEFAULT 10000)
LANGUAGE plpgsql
AS $$
DECLARE
    v_start_ts TIMESTAMP := clock_timestamp();
    v_num_cinemas INT;
    v_num_halls_per_cinema INT;
    v_num_seats_per_hall INT;
    v_num_movies INT;
    v_num_sessions_per_hall INT;
    v_tickets_fill_percent FLOAT;
BEGIN
    RAISE NOTICE 'Очистка всех таблиц...';
    TRUNCATE TABLE ticket, session, attribute_values, attribute, attribute_type, 
                    seat, seat_category, hall, cinema, movie 
    RESTART IDENTITY CASCADE;
    
    IF p_target_rows <= 10000 THEN
        -- Расчёт: 5 залов * 50 сеансов * 80 мест * 0.5 = 10,000 билетов
        v_num_cinemas := 1;
        v_num_halls_per_cinema := 5;       -- 5 залов
        v_num_seats_per_hall := 80;        -- 400 мест всего
        v_num_movies := 30;
        v_num_sessions_per_hall := 50;     -- 250 сеансов
        v_tickets_fill_percent := 0.5;     -- ~10,000 билетов
    ELSE
        v_num_cinemas := 3;
        v_num_halls_per_cinema := 20;      -- 60 залов
        v_num_seats_per_hall := 200;       -- 12,000 мест
        v_num_movies := 100;
        v_num_sessions_per_hall := 2000;   -- 120,000 сеансов
        v_tickets_fill_percent := 0.4;     -- ~9,600,000 билетов
    END IF;

    RAISE NOTICE '========================================';
    RAISE NOTICE 'Генерация тестовых данных';
    RAISE NOTICE 'Целевое кол-во записей: %', p_target_rows;
    RAISE NOTICE 'Кинотеатров: %, Залов: %, Мест/зал: %', 
        v_num_cinemas, v_num_cinemas * v_num_halls_per_cinema, v_num_seats_per_hall;
    RAISE NOTICE 'Фильмов: %, Сеансов/зал: %', v_num_movies, v_num_sessions_per_hall;
    RAISE NOTICE '========================================';

    -- 1. КИНОТЕАТРЫ
    RAISE NOTICE 'Создание кинотеатров...';
    
    INSERT INTO cinema (name)
    SELECT 
        CASE (gs % 10)
            WHEN 0 THEN 'Кинотеатр «Звезда» #'
            WHEN 1 THEN 'Cinema Park #'
            WHEN 2 THEN 'Киномакс #'
            WHEN 3 THEN 'Formula Kino #'
            WHEN 4 THEN 'Люксор #'
            WHEN 5 THEN 'Синема Стар #'
            WHEN 6 THEN 'Каро Фильм #'
            WHEN 7 THEN 'Пять звёзд #'
            WHEN 8 THEN 'Мир Кино #'
            ELSE 'Родина #'
        END || gs
    FROM generate_series(1, v_num_cinemas) AS gs;

    -- 2. КАТЕГОРИИ МЕСТ
    RAISE NOTICE 'Создание категорий мест...';
    
    INSERT INTO seat_category (name, price_multiplier) VALUES
        ('Стандарт', 1.00),
        ('Премиум', 1.50),
        ('Супер Премиум', 2.00)
    ON CONFLICT DO NOTHING;

    -- 3. ЗАЛЫ
    RAISE NOTICE 'Создание залов...';
    
    INSERT INTO hall (cinema_id, name)
    SELECT 
        c.id,
        'Зал ' || h.n || ' - ' ||
        CASE (h.n % 5)
            WHEN 1 THEN 'Большой'
            WHEN 2 THEN 'Средний'
            WHEN 3 THEN 'VIP'
            WHEN 4 THEN 'IMAX'
            ELSE '3D'
        END
    FROM cinema c
    CROSS JOIN generate_series(1, v_num_halls_per_cinema) AS h(n);

    -- 4. МЕСТА В ЗАЛАХ
    RAISE NOTICE 'Создание мест в залах...';
    
    -- Распределяем места: 10 рядов, категории по рядам
    INSERT INTO seat (hall_id, nrow, seat_number, seat_category_id)
    SELECT 
        h.id,
        (gs - 1) / (v_num_seats_per_hall / 10) + 1 AS nrow,
        (gs - 1) % (v_num_seats_per_hall / 10) + 1 AS seat_number,
        CASE 
            WHEN (gs - 1) / (v_num_seats_per_hall / 10) + 1 <= 4 THEN 1  -- Ряды 1-4: Стандарт
            WHEN (gs - 1) / (v_num_seats_per_hall / 10) + 1 <= 7 THEN 2  -- Ряды 5-7: Премиум
            ELSE 3  -- Ряды 8-10: Супер Премиум
        END
    FROM hall h
    CROSS JOIN generate_series(1, v_num_seats_per_hall) AS gs;

    -- 5. ФИЛЬМЫ (максимум 100)
    RAISE NOTICE 'Создание % фильмов...', v_num_movies;
    
    INSERT INTO movie (title, duration, description)
    SELECT 
        CASE (gs % 20)
            WHEN 0 THEN 'Интерстеллар'
            WHEN 1 THEN 'Начало'
            WHEN 2 THEN 'Матрица'
            WHEN 3 THEN 'Аватар'
            WHEN 4 THEN 'Дюна'
            WHEN 5 THEN 'Оппенгеймер'
            WHEN 6 THEN 'Тёмный рыцарь'
            WHEN 7 THEN 'Властелин колец'
            WHEN 8 THEN 'Звёздные войны'
            WHEN 9 THEN 'Титаник'
            WHEN 10 THEN 'Гладиатор'
            WHEN 11 THEN 'Форрест Гамп'
            WHEN 12 THEN 'Зелёная миля'
            WHEN 13 THEN 'Побег из Шоушенка'
            WHEN 14 THEN 'Крёстный отец'
            WHEN 15 THEN 'Бойцовский клуб'
            WHEN 16 THEN 'Леон'
            WHEN 17 THEN 'Джокер'
            WHEN 18 THEN 'Бедные-несчастные'
            ELSE 'Мстители'
        END || ' #' || gs AS title,
        80 + (random() * 120)::INT AS duration,
        'Захватывающий фильм #' || gs || '. Невероятные спецэффекты и потрясающая игра актёров.' AS description
    FROM generate_series(1, v_num_movies) AS gs;

    -- 6. ТИПЫ АТРИБУТОВ (EAV)
    RAISE NOTICE 'Создание типов атрибутов...';
    
    INSERT INTO attribute_type (name, data_type) VALUES
        ('Рецензии', 'string'),
        ('Премии', 'boolean'),
        ('Важные даты', 'date'),
        ('Служебные даты', 'date'),
        ('Рейтинги', 'numeric'),
        ('Статистика', 'int')
    ON CONFLICT (name) DO NOTHING;
    
    -- 7. АТРИБУТЫ (EAV)
    RAISE NOTICE 'Создание атрибутов...';
    
    INSERT INTO attribute (attr_type_id, name, description)
    SELECT at.id, a.name, a.description
    FROM (
        VALUES
            ('Рецензии', 'Рецензия критика', 'Основная рецензия профильного критика'),
            ('Рецензии', 'Отзыв киноакадемии', 'Комментарий киноакадемии'),
            ('Рецензии', 'Рецензия прессы', 'Краткий отзыв из прессы'),
            ('Премии', 'Оскар', 'Наличие премии Оскар'),
            ('Премии', 'Ника', 'Наличие премии Ника'),
            ('Премии', 'Золотой глобус', 'Наличие премии Золотой глобус'),
            ('Важные даты', 'Мировая премьера', 'Дата мировой премьеры фильма'),
            ('Важные даты', 'Премьера в РФ', 'Дата премьеры в России'),
            ('Служебные даты', 'Старт продаж билетов', 'Когда открываются продажи'),
            ('Служебные даты', 'Запуск рекламы на ТВ', 'Дата старта рекламной кампании'),
            ('Рейтинги', 'Рейтинг IMDB', 'Оценка на IMDB'),
            ('Рейтинги', 'Рейтинг Кинопоиск', 'Оценка на Кинопоиске'),
            ('Статистика', 'Количество просмотров', 'Общее число просмотров'),
            ('Статистика', 'Количество отзывов', 'Число пользовательских отзывов')
    ) AS a(attr_type_name, name, description)
    JOIN attribute_type at ON at.name = a.attr_type_name
    ON CONFLICT (name) DO NOTHING;

    -- 8. ЗНАЧЕНИЯ АТРИБУТОВ (EAV)
    RAISE NOTICE 'Заполнение значений атрибутов для фильмов...';
    
    -- Рецензии (text)
    INSERT INTO attribute_values (movie_id, attribute_id, value_string)
    SELECT m.id, a.id, 
        CASE a.name
            WHEN 'Рецензия критика' THEN 'Отличный фильм #' || m.id || ' — профессиональная работа режиссёра.'
            WHEN 'Отзыв киноакадемии' THEN 'Фильм #' || m.id || ' заслуживает высокой оценки.'
            ELSE 'Краткий отзыв прессы о фильме #' || m.id || '.'
        END
    FROM movie m
    CROSS JOIN attribute a
    WHERE a.name IN ('Рецензия критика', 'Отзыв киноакадемии', 'Рецензия прессы');

    -- Премии (boolean)
    INSERT INTO attribute_values (movie_id, attribute_id, value_boolean)
    SELECT m.id, a.id, (random() > 0.7)
    FROM movie m
    CROSS JOIN attribute a
    WHERE a.name IN ('Оскар', 'Ника', 'Золотой глобус');

    -- Важные даты (date)
    INSERT INTO attribute_values (movie_id, attribute_id, value_date)
    SELECT m.id, a.id, 
        CURRENT_DATE - ((random() * 365)::INT || ' days')::INTERVAL
    FROM movie m
    CROSS JOIN attribute a
    WHERE a.name IN ('Мировая премьера', 'Премьера в РФ');

    -- Служебные даты (date)
    INSERT INTO attribute_values (movie_id, attribute_id, value_date)
    SELECT m.id, a.id, 
        CURRENT_DATE + ((random() * 90)::INT || ' days')::INTERVAL
    FROM movie m
    CROSS JOIN attribute a
    WHERE a.name IN ('Старт продаж билетов', 'Запуск рекламы на ТВ');

    -- Рейтинги (numeric)
    INSERT INTO attribute_values (movie_id, attribute_id, value_numeric)
    SELECT m.id, a.id, 
        (5.0 + random() * 5.0)::NUMERIC(10,2)
    FROM movie m
    CROSS JOIN attribute a
    WHERE a.name IN ('Рейтинг IMDB', 'Рейтинг Кинопоиск');

    -- Статистика (int)
    INSERT INTO attribute_values (movie_id, attribute_id, value_int)
    SELECT m.id, a.id, 
        (random() * 1000000)::INT
    FROM movie m
    CROSS JOIN attribute a
    WHERE a.name IN ('Количество просмотров', 'Количество отзывов');

    -- 9. СЕАНСЫ (sessions)
    RAISE NOTICE 'Создание сеансов (% на зал)...', v_num_sessions_per_hall;
    
    INSERT INTO session (movie_id, hall_id, start_time, base_price)
    SELECT 
        -- Равномерное распределение фильмов: каждый фильм получает примерно одинаковое кол-во сеансов
        ((ROW_NUMBER() OVER () - 1) % v_num_movies) + 1 AS movie_id,
        h.id AS hall_id,
        -- Распределяем сеансы по дням: больше сеансов на текущий день и ближайшие
        CURRENT_DATE - INTERVAL '7 days' + 
            ((s.n - 1) % 14 || ' days')::INTERVAL + 
            ((8 + (s.n % 5) * 2 + (random() * 2)::INT) || ' hours')::INTERVAL AS start_time,
        (200 + random() * 300)::NUMERIC(10,2) AS base_price
    FROM hall h
    CROSS JOIN generate_series(1, v_num_sessions_per_hall) AS s(n);

    -- 10. БИЛЕТЫ
    RAISE NOTICE 'Создание билетов (заполнение ~%.0f%% мест)...', v_tickets_fill_percent * 100;
    
    INSERT INTO ticket (session_id, seat_id, status, total_price, purchased_at)
    SELECT DISTINCT ON (s.id, se.id)
        s.id,
        se.id,
        CASE (random() * 10)::INT
            WHEN 0 THEN 'reserved'::ticket_status_enum
            WHEN 1 THEN 'cancelled'::ticket_status_enum
            WHEN 2 THEN 'used'::ticket_status_enum
            ELSE 'paid'::ticket_status_enum
        END,
        s.base_price * sc.price_multiplier,
        CURRENT_DATE - ((random() * 6)::INT || ' days')::INTERVAL + 
            ((random() * 23)::INT || ' hours')::INTERVAL
    FROM session s
    INNER JOIN hall h ON s.hall_id = h.id
    INNER JOIN seat se ON se.hall_id = h.id
    INNER JOIN seat_category sc ON se.seat_category_id = sc.id
    WHERE random() < v_tickets_fill_percent;

    -- РЕЗУЛЬТАТ РАБОТЫ ПРОЦЕДУРЫ
    RAISE NOTICE '========================================';
    RAISE NOTICE 'ГЕНЕРАЦИЯ ЗАВЕРШЕНА за % сек', 
        ROUND(EXTRACT(EPOCH FROM clock_timestamp() - v_start_ts)::NUMERIC, 2);
    RAISE NOTICE '========================================';
    RAISE NOTICE 'Кинотеатров:        %', (SELECT COUNT(*) FROM cinema);
    RAISE NOTICE 'Залов:              %', (SELECT COUNT(*) FROM hall);
    RAISE NOTICE 'Категорий мест:     %', (SELECT COUNT(*) FROM seat_category);
    RAISE NOTICE 'Мест:               %', (SELECT COUNT(*) FROM seat);
    RAISE NOTICE 'Фильмов:            %', (SELECT COUNT(*) FROM movie);
    RAISE NOTICE 'Типов атрибутов:    %', (SELECT COUNT(*) FROM attribute_type);
    RAISE NOTICE 'Атрибутов:          %', (SELECT COUNT(*) FROM attribute);
    RAISE NOTICE 'Значений атрибутов: %', (SELECT COUNT(*) FROM attribute_values);
    RAISE NOTICE 'Сеансов:            %', (SELECT COUNT(*) FROM session);
    RAISE NOTICE 'Билетов:            %', (SELECT COUNT(*) FROM ticket);
    RAISE NOTICE '----------------------------------------';
    RAISE NOTICE 'ВСЕГО ЗАПИСЕЙ:      %', (
        (SELECT COUNT(*) FROM cinema) +
        (SELECT COUNT(*) FROM hall) +
        (SELECT COUNT(*) FROM seat_category) +
        (SELECT COUNT(*) FROM seat) +
        (SELECT COUNT(*) FROM movie) +
        (SELECT COUNT(*) FROM attribute_type) +
        (SELECT COUNT(*) FROM attribute) +
        (SELECT COUNT(*) FROM attribute_values) +
        (SELECT COUNT(*) FROM session) +
        (SELECT COUNT(*) FROM ticket)
    );
    RAISE NOTICE '========================================';
    
END;
$$;
