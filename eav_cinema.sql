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

-- Новые таблицы

-- Таблица кинозалов
CREATE TABLE cinema_halls (
    hall_id SERIAL PRIMARY KEY,
    hall_name VARCHAR(100) NOT NULL,
    total_seats INT NOT NULL CHECK (total_seats > 0),
    description TEXT
);

-- Таблица сеансов 
CREATE TABLE sessions (
    session_id SERIAL PRIMARY KEY,
    film_id INT NOT NULL REFERENCES films(film_id),
    hall_id INT NOT NULL REFERENCES cinema_halls(hall_id),
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP NOT NULL,
    base_price DECIMAL(10,2) NOT NULL CHECK (base_price > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CHECK (end_time > start_time)
);

-- Таблица мест в зале
CREATE TABLE hall_seats (
    seat_id SERIAL PRIMARY KEY,
    hall_id INT NOT NULL REFERENCES cinema_halls(hall_id),
    row_number INT NOT NULL CHECK (row_number > 0),
    seat_number INT NOT NULL CHECK (seat_number > 0),
    seat_type VARCHAR(20) DEFAULT 'standard' CHECK (seat_type IN ('standard', 'vip', 'disabled')),
    price_multiplier DOUBLE PRECISION DEFAULT 1.00 CHECK (price_multiplier > 0),
    
    UNIQUE(hall_id, row_number, seat_number)
);

-- Таблица билетов
CREATE TABLE tickets (
    ticket_id BIGSERIAL PRIMARY KEY,
    session_id INT NOT NULL REFERENCES sessions(session_id),
    seat_id INT NOT NULL REFERENCES hall_seats(seat_id),
    customer_name VARCHAR(255),
    customer_email VARCHAR(255),
    ticket_price DECIMAL(10,2) NOT NULL CHECK (ticket_price > 0),
    purchase_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'sold' CHECK (status IN ('sold', 'reserved', 'cancelled')),
    
    UNIQUE(session_id, seat_id)
);

-- Таблица динамического ценообразования
CREATE TABLE dynamic_pricing (
    price_id SERIAL PRIMARY KEY,
    session_id INT NOT NULL REFERENCES sessions(session_id),
    multiplier DOUBLE PRECISION NOT NULL CHECK (multiplier > 0),
    apply_from TIMESTAMP NOT NULL,
    apply_to TIMESTAMP NOT NULL,
    reason VARCHAR(100),
    
    CHECK (apply_to > apply_from)
);

-- Таблица отзывов о фильмах
CREATE TABLE film_reviews (
    review_id BIGSERIAL PRIMARY KEY,
    film_id INT NOT NULL REFERENCES films(film_id),
    customer_name VARCHAR(255),
    rating INT CHECK (rating >= 1 AND rating <= 10),
    review_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    helpful_count INT DEFAULT 0
);

-- Индексы (основные)
CREATE INDEX idx_attr_values_film ON attribute_values(film_id);
CREATE INDEX idx_attr_values_attr ON attribute_values(attr_id);
CREATE INDEX idx_attr_values_date ON attribute_values(date_value);

-- Индексы для сеансов
CREATE INDEX idx_sessions_film ON sessions(film_id);
CREATE INDEX idx_sessions_hall ON sessions(hall_id);
CREATE INDEX idx_sessions_time ON sessions(start_time);
CREATE INDEX idx_sessions_film_time ON sessions(film_id, start_time);
CREATE INDEX idx_sessions_date ON sessions(DATE(start_time)); -- Для фильтрации по дате

-- Индексы для билетов
CREATE INDEX idx_tickets_session ON tickets(session_id);
CREATE INDEX idx_tickets_seat ON tickets(seat_id);
CREATE INDEX idx_tickets_purchase_time ON tickets(purchase_time);
CREATE INDEX idx_tickets_status ON tickets(status);
CREATE INDEX idx_tickets_session_seat ON tickets(session_id, seat_id);
CREATE INDEX idx_tickets_date_status ON tickets(DATE(purchase_time), status); -- Для недельных отчетов

-- Индексы для мест
CREATE INDEX idx_seats_hall ON hall_seats(hall_id);
CREATE INDEX idx_seats_type ON hall_seats(seat_type);

-- Индексы для отзывов (если будем добавлять таблицу film_reviews позже)
CREATE INDEX idx_reviews_film ON film_reviews(film_id);
CREATE INDEX idx_reviews_rating ON film_reviews(rating);

-- Индексы для динамического ценообразования
CREATE INDEX idx_pricing_session ON dynamic_pricing(session_id);


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

-- Функция для заполнения данных о кинозалах и сеансах
CREATE OR REPLACE FUNCTION fill_cinema_data() 
RETURNS VOID AS $$
DECLARE
    hall_rec RECORD;
    film_rec RECORD;
    i INT;
    session_time TIMESTAMP;
BEGIN
    -- Очищаем таблицы в правильном порядке
    DELETE FROM tickets;
    DELETE FROM dynamic_pricing;
    DELETE FROM hall_seats;
    DELETE FROM sessions;
    DELETE FROM cinema_halls;
    
    -- Создаем кинозалы
    INSERT INTO cinema_halls (hall_name, total_seats, description) VALUES
    ('Зал 1 - Большой', 200, 'Основной зал с панорамным экраном'),
    ('Зал 2 - Средний', 120, 'Комфортный зал с улучшенным звуком'),
    ('Зал 3 - Малый', 80, 'Камерный зал для премьер'),
    ('Зал 4 - IMAX', 150, 'Зал с технологией IMAX'),
    ('Зал 5 - VIP', 60, 'Зал с повышенным комфортом');
    
    -- Для каждого зала создаем места
    FOR hall_rec IN SELECT * FROM cinema_halls LOOP
        -- Места в зале
        FOR i IN 1..hall_rec.total_seats LOOP
            INSERT INTO hall_seats (hall_id, row_number, seat_number, seat_type, price_multiplier)
            VALUES (
                hall_rec.hall_id,
                (i / 20) + 1, -- ряд
                (i % 20) + 1, -- место в ряду
                CASE 
                    WHEN (i / 20) + 1 <= 3 THEN 'vip'
                    WHEN (i % 20) + 1 IN (1, 2, 19, 20) THEN 'disabled'
                    ELSE 'standard'
                END,
                CASE 
                    WHEN (i / 20) + 1 <= 3 THEN 1.5::DOUBLE PRECISION
                    WHEN (i % 20) + 1 IN (1, 2, 19, 20) THEN 0.8::DOUBLE PRECISION
                    ELSE 1.0::DOUBLE PRECISION
                END
            );
        END LOOP;
    END LOOP;
    
    -- Сеансы на ближайшие 7 дней
    FOR film_rec IN SELECT * FROM films LOOP
        FOR i IN 0..6 LOOP -- На 7 дней вперед
            session_time := (CURRENT_DATE + i) + TIME '10:00' + (floor(random() * 5) * INTERVAL '2 hours');
            
            INSERT INTO sessions (film_id, hall_id, start_time, end_time, base_price)
            VALUES (
                film_rec.film_id,
                1 + floor(random() * 5)::int, -- случайный зал
                session_time,
                session_time + INTERVAL '2 hours' + INTERVAL '15 minutes', -- фильм + реклама
                300 + floor(random() * 200)::int -- цена от 300 до 500
            );
        END LOOP;
    END LOOP;
    
    -- Создаем динамическое ценообразование для некоторых сеансов
    INSERT INTO dynamic_pricing (session_id, multiplier, apply_from, apply_to, reason)
    SELECT 
        session_id,
        1.2::DOUBLE PRECISION,
        start_time - INTERVAL '3 days',
        start_time - INTERVAL '1 day',
        'Предпродажа'
    FROM sessions 
    WHERE EXTRACT(DOW FROM start_time) IN (5, 6) -- выходные
    LIMIT 10;
    
    RAISE NOTICE 'Данные кинотеатра загружены!';
    RAISE NOTICE 'Кинозалов: %', (SELECT COUNT(*) FROM cinema_halls);
    RAISE NOTICE 'Мест: %', (SELECT COUNT(*) FROM hall_seats);
    RAISE NOTICE 'Сеансов: %', (SELECT COUNT(*) FROM sessions);
END;
$$ LANGUAGE plpgsql;

-- Функция для заполнения билетами
CREATE OR REPLACE FUNCTION fill_tickets_data(ticket_count INT DEFAULT 1000) 
RETURNS VOID AS $$
DECLARE
    session_rec RECORD;
    seat_rec RECORD;
    i INT;
    tickets_created INT := 0;
BEGIN
    DELETE FROM tickets;
    
    FOR session_rec IN 
        SELECT s.*, h.hall_id 
        FROM sessions s 
        JOIN cinema_halls h ON s.hall_id = h.hall_id 
        WHERE s.start_time > CURRENT_TIMESTAMP - INTERVAL '7 days'
        AND s.start_time < CURRENT_TIMESTAMP + INTERVAL '1 day'
        ORDER BY random()
    LOOP
        -- Получаем случайные места для этого зала
        FOR seat_rec IN 
            SELECT hs.* 
            FROM hall_seats hs 
            WHERE hs.hall_id = session_rec.hall_id 
            ORDER BY random() 
            LIMIT floor(random() * 0.8 * (SELECT total_seats FROM cinema_halls WHERE hall_id = session_rec.hall_id))::int + 1
        LOOP
            -- Проверяем, нет ли уже билета на это место
            IF NOT EXISTS (
                SELECT 1 FROM tickets t 
                WHERE t.session_id = session_rec.session_id 
                AND t.seat_id = seat_rec.seat_id
            ) THEN
                INSERT INTO tickets (
                    session_id, 
                    seat_id, 
                    customer_name, 
                    customer_email, 
                    ticket_price, 
                    purchase_time, 
                    status
                ) VALUES (
                    session_rec.session_id,
                    seat_rec.seat_id,
                    'Клиент ' || (1000 + tickets_created),
                    'client' || (1000 + tickets_created) || '@mail.ru',
                    (session_rec.base_price * seat_rec.price_multiplier)::DECIMAL(10,2),
                    session_rec.start_time - INTERVAL '1 day' + random() * INTERVAL '23 hours',
                    CASE WHEN random() < 0.05 THEN 'cancelled' ELSE 'sold' END
                );
                
                tickets_created := tickets_created + 1;
                
                -- Останавливаемся, если достигли нужного количества
                EXIT WHEN tickets_created >= ticket_count;
            END IF;
        END LOOP;
        
        EXIT WHEN tickets_created >= ticket_count;
    END LOOP;
    
    RAISE NOTICE 'Создано билетов: %', tickets_created;
END;
$$ LANGUAGE plpgsql;

-- Функция для массового заполнения текстовых данных (отзывов)
CREATE OR REPLACE FUNCTION fill_reviews_data(review_count INT DEFAULT 10000) 
RETURNS VOID AS $$
DECLARE
    film_rec RECORD;
    reviews_per_film INT;
    j INT;
    review_texts TEXT[] := ARRAY[
        'Отличный фильм! Очень понравилось.',
        'Интересный сюжет, хорошая актерская игра.',
        'Не ожидал такого финала, впечатляет.',
        'Можно было сделать лучше, но в целом неплохо.',
        'Захватывающее кино, рекомендую всем!',
        'Немного затянуто, но идея интересная.',
        'Визуальные эффекты просто потрясающие.',
        'Хороший фильм для семейного просмотра.',
        'Не самый лучший, но посмотреть можно.',
        'Настоящий шедевр кинематографа!',
        'Разочарован, ожидал большего.',
        'Отличная режиссерская работа.',
        'Музыкальное сопровождение на высоте.',
        'Сюжет предсказуем, но смотрибельно.',
        'Один из лучших фильмов года!'
    ];
    customer_names TEXT[] := ARRAY[
        'Александр', 'Мария', 'Иван', 'Елена', 'Дмитрий', 'Ольга', 'Сергей', 'Анна'
    ];
BEGIN
    -- Очищаем таблицу
    DELETE FROM film_reviews;
    
    -- Распределяем отзывы по фильмам
    FOR film_rec IN SELECT * FROM films LOOP
        reviews_per_film := (review_count / (SELECT COUNT(*) FROM films)) + floor(random() * 5)::int;
        
        FOR j IN 1..reviews_per_film LOOP
            INSERT INTO film_reviews (
                film_id,
                customer_name,
                rating,
                review_text,
                created_at,
                helpful_count
            ) VALUES (
                film_rec.film_id,
                customer_names[1 + floor(random() * array_length(customer_names, 1))::int],
                1 + floor(random() * 10)::int,
                review_texts[1 + floor(random() * array_length(review_texts, 1))::int] || ' ' ||
                CASE WHEN random() < 0.3 THEN 'Особенно понравилась игра актеров.' ELSE '' END ||
                CASE WHEN random() < 0.3 THEN ' Рекомендую к просмотру.' ELSE '' END,
                CURRENT_TIMESTAMP - (random() * INTERVAL '365 days'),
                floor(random() * 100)::int
            );
        END LOOP;
    END LOOP;
    
    -- Анализируем таблицу для оптимизации
    ANALYZE film_reviews;
    
    RAISE NOTICE 'Создано отзывов: %', (SELECT COUNT(*) FROM film_reviews);
END;
$$ LANGUAGE plpgsql;

-- Функция для тестирования производительности запросов
CREATE OR REPLACE FUNCTION test_query_performance()
RETURNS TABLE (
    query_name TEXT,
    data_size TEXT,
    execution_time_ms NUMERIC,
    plan TEXT
) AS $$
DECLARE
    start_time TIMESTAMP;
    end_time TIMESTAMP;
    query_plan TEXT;
    test_session_id INT;
BEGIN
    -- Получаем ID сеанса для тестирования
    SELECT session_id INTO test_session_id FROM sessions LIMIT 1;
    
    -- Фильмы на сегодня
    start_time := clock_timestamp();
    EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT) 
    SELECT * FROM films_today
    INTO query_plan;
    end_time := clock_timestamp();
    
    RETURN QUERY SELECT 
        '1. Фильмы на сегодня',
        (SELECT pg_size_pretty(pg_total_relation_size('films') + pg_total_relation_size('sessions'))),
        EXTRACT(MILLISECONDS FROM end_time - start_time),
        query_plan;
    
    -- Билеты за неделю
    start_time := clock_timestamp();
    EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
    SELECT * FROM weekly_tickets
    INTO query_plan;
    end_time := clock_timestamp();
    
    RETURN QUERY SELECT 
        '2. Билеты за неделю',
        (SELECT pg_size_pretty(pg_total_relation_size('tickets'))),
        EXTRACT(MILLISECONDS FROM end_time - start_time),
        query_plan;
    
    -- Афиша на сегодня
    start_time := clock_timestamp();
    EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
    SELECT * FROM today_poster
    INTO query_plan;
    end_time := clock_timestamp();
    
    RETURN QUERY SELECT 
        '3. Афиша на сегодня',
        (SELECT pg_size_pretty(pg_total_relation_size('films') + pg_total_relation_size('sessions') + pg_total_relation_size('cinema_halls'))),
        EXTRACT(MILLISECONDS FROM end_time - start_time),
        query_plan;
    
    -- Топ фильмов
    start_time := clock_timestamp();
    EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
    SELECT * FROM top_profitable_films
    INTO query_plan;
    end_time := clock_timestamp();
    
    RETURN QUERY SELECT 
        '4. Топ фильмов за неделю',
        (SELECT pg_size_pretty(pg_total_relation_size('films') + pg_total_relation_size('sessions') + pg_total_relation_size('tickets'))),
        EXTRACT(MILLISECONDS FROM end_time - start_time),
        query_plan;
    
    -- Схема зала
    start_time := clock_timestamp();
    EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
    SELECT * FROM hall_scheme_view WHERE session_id = test_session_id LIMIT 50
    INTO query_plan;
    end_time := clock_timestamp();
    
    RETURN QUERY SELECT 
        '5. Схема зала',
        (SELECT pg_size_pretty(pg_total_relation_size('sessions') + pg_total_relation_size('hall_seats') + pg_total_relation_size('tickets'))),
        EXTRACT(MILLISECONDS FROM end_time - start_time),
        query_plan;
    
    -- Диапазон цен
    start_time := clock_timestamp();
    EXPLAIN (ANALYZE, BUFFERS, FORMAT TEXT)
    SELECT * FROM ticket_price_ranges WHERE session_id = test_session_id
    INTO query_plan;
    end_time := clock_timestamp();
    
    RETURN QUERY SELECT 
        '6. Диапазон цен',
        (SELECT pg_size_pretty(pg_total_relation_size('sessions') + pg_total_relation_size('hall_seats'))),
        EXTRACT(MILLISECONDS FROM end_time - start_time),
        query_plan;
END;
$$ LANGUAGE plpgsql;

-- Функция для анализа самых больших объектов БД
CREATE OR REPLACE FUNCTION analyze_largest_objects()
RETURNS TABLE (
    object_name TEXT,
    object_type TEXT,
    size TEXT,
    total_size_mb NUMERIC
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        schemaname || '.' || tablename as object_name,
        'TABLE' as object_type,
        pg_size_pretty(pg_total_relation_size(schemaname || '.' || tablename)) as size,
        pg_total_relation_size(schemaname || '.' || tablename) / (1024*1024)::numeric as total_size_mb
    FROM pg_tables
    WHERE schemaname = 'public'
    UNION ALL
    SELECT 
        schemaname || '.' || indexname as object_name,
        'INDEX' as object_type,
        pg_size_pretty(pg_relation_size(schemaname || '.' || indexname)) as size,
        pg_relation_size(schemaname || '.' || indexname) / (1024*1024)::numeric as total_size_mb
    FROM pg_indexes
    WHERE schemaname = 'public'
    ORDER BY total_size_mb DESC
    LIMIT 15;
END;
$$ LANGUAGE plpgsql;

-- Функция для анализа использования индексов
CREATE OR REPLACE FUNCTION analyze_index_usage()
RETURNS TABLE (
    index_name TEXT,
    table_name TEXT,
    index_size TEXT,
    scans BIGINT,
    reads BIGINT,
    fetches BIGINT
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        schemaname || '.' || indexrelname as index_name,
        schemaname || '.' || relname as table_name,
        pg_size_pretty(pg_relation_size(indexrelid)) as index_size,
        idx_scan as scans,
        idx_tup_read as reads,
        idx_tup_fetch as fetches
    FROM pg_stat_user_indexes
    ORDER BY idx_scan DESC
    LIMIT 10;
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

-- Дополнительные представления
-- Выбор всех фильмов на сегодня
CREATE OR REPLACE VIEW films_today AS
SELECT DISTINCT f.film_id, f.title, f.release_year
FROM films f
JOIN sessions s ON f.film_id = s.film_id
WHERE DATE(s.start_time) = CURRENT_DATE
ORDER BY f.title;

-- Подсчёт проданных билетов за неделю
CREATE OR REPLACE VIEW weekly_tickets AS
SELECT 
    DATE(purchase_time) as sale_date,
    COUNT(*) as tickets_sold,
    SUM(ticket_price) as total_revenue
FROM tickets 
WHERE purchase_time >= CURRENT_DATE - INTERVAL '7 days'
    AND status = 'sold'
GROUP BY DATE(purchase_time)
ORDER BY sale_date DESC;

-- Формируем афиши (фильмы, которые показывают сегодня)
CREATE OR REPLACE VIEW today_poster AS
SELECT 
    f.title,
    s.start_time,
    s.end_time,
    ch.hall_name,
    s.base_price as base_ticket_price,
    (SELECT COUNT(*) FROM tickets t WHERE t.session_id = s.session_id AND t.status = 'sold') as tickets_sold,
    ch.total_seats - (SELECT COUNT(*) FROM tickets t WHERE t.session_id = s.session_id AND t.status = 'sold') as seats_available
FROM films f
JOIN sessions s ON f.film_id = s.film_id
JOIN cinema_halls ch ON s.hall_id = ch.hall_id
WHERE DATE(s.start_time) = CURRENT_DATE
ORDER BY s.start_time, ch.hall_name;

-- Поиск 3 самых прибыльных фильмов за неделю
CREATE OR REPLACE VIEW top_profitable_films AS
SELECT 
    f.title,
    COUNT(DISTINCT s.session_id) as sessions_count,
    COUNT(t.ticket_id) as tickets_sold,
    SUM(t.ticket_price) as total_revenue,
    ROUND(AVG(t.ticket_price)::numeric, 2) as avg_ticket_price
FROM films f
JOIN sessions s ON f.film_id = s.film_id
LEFT JOIN tickets t ON s.session_id = t.session_id AND t.status = 'sold'
WHERE t.purchase_time >= CURRENT_DATE - INTERVAL '7 days'
    OR t.purchase_time IS NULL
GROUP BY f.film_id, f.title
ORDER BY total_revenue DESC NULLS LAST
LIMIT 3;

-- Формируем схему зала
CREATE OR REPLACE VIEW hall_scheme_view AS
SELECT 
    s.session_id,
    f.title as film_title,
    s.start_time,
    ch.hall_name,
    hs.row_number,
    hs.seat_number,
    hs.seat_type,
    CASE 
        WHEN t.ticket_id IS NOT NULL AND t.status = 'sold' THEN 'sold'
        WHEN t.ticket_id IS NOT NULL AND t.status = 'reserved' THEN 'reserved'
        ELSE 'free'
    END as seat_status,
    (s.base_price * hs.price_multiplier)::DECIMAL(10,2) as ticket_price
FROM sessions s
JOIN films f ON s.film_id = f.film_id
JOIN cinema_halls ch ON s.hall_id = ch.hall_id
JOIN hall_seats hs ON s.hall_id = hs.hall_id
LEFT JOIN tickets t ON s.session_id = t.session_id AND hs.seat_id = t.seat_id
ORDER BY s.session_id, hs.row_number, hs.seat_number;

-- Вывод диапазона цен
CREATE OR REPLACE VIEW ticket_price_ranges AS
SELECT 
    s.session_id,
    f.title as film_title,
    s.start_time,
    ch.hall_name,
    MIN((s.base_price * hs.price_multiplier)::DECIMAL(10,2)) as min_price,
    MAX((s.base_price * hs.price_multiplier)::DECIMAL(10,2)) as max_price,
    ROUND(AVG((s.base_price * hs.price_multiplier)::DECIMAL(10,2))::numeric, 2) as avg_price,
    COUNT(hs.seat_id) as total_seats,
    COUNT(t.ticket_id) as sold_seats,
    ROUND(100.0 * COUNT(t.ticket_id) / COUNT(hs.seat_id), 1) as occupancy_percent
FROM sessions s
JOIN films f ON s.film_id = f.film_id
JOIN cinema_halls ch ON s.hall_id = ch.hall_id
JOIN hall_seats hs ON s.hall_id = hs.hall_id
LEFT JOIN tickets t ON s.session_id = t.session_id AND hs.seat_id = t.seat_id AND t.status = 'sold'
GROUP BY s.session_id, f.title, s.start_time, ch.hall_name
ORDER BY s.start_time;
-- Конец представлений


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

-- Формирование схемы зала
CREATE OR REPLACE FUNCTION hall_scheme(session_id_param INT)
RETURNS TABLE (
    row_number INT,
    seat_number INT,
    seat_type VARCHAR(20),
    status VARCHAR(20),
    price DECIMAL(10,2)
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        hs.row_number,
        hs.seat_number,
        hs.seat_type,
        CASE 
            WHEN t.ticket_id IS NOT NULL AND t.status = 'sold' THEN 'sold'
            WHEN t.ticket_id IS NOT NULL AND t.status = 'reserved' THEN 'reserved'
            ELSE 'free'
        END as status,
        (s.base_price * hs.price_multiplier)::DECIMAL(10,2) as price
    FROM sessions s
    JOIN hall_seats hs ON s.hall_id = hs.hall_id
    LEFT JOIN tickets t ON s.session_id = t.session_id AND hs.seat_id = t.seat_id
    WHERE s.session_id = session_id_param
    ORDER BY hs.row_number, hs.seat_number;
END;
$$ LANGUAGE plpgsql;

-- Вывод диапазона цен
CREATE OR REPLACE FUNCTION ticket_price_range(session_id_param INT)
RETURNS TABLE (
    min_price DECIMAL(10,2),
    max_price DECIMAL(10,2),
    avg_price DECIMAL(10,2),
    seats_count INT,
    sold_seats INT
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        MIN((s.base_price * hs.price_multiplier)::DECIMAL(10,2)) as min_price, 
        MAX((s.base_price * hs.price_multiplier)::DECIMAL(10,2)) as max_price, 
        AVG((s.base_price * hs.price_multiplier)::DECIMAL(10,2)) as avg_price,
        COUNT(hs.seat_id) as seats_count,
        COUNT(t.ticket_id) as sold_seats
    FROM sessions s
    JOIN hall_seats hs ON s.hall_id = hs.hall_id
    LEFT JOIN tickets t ON s.session_id = t.session_id AND hs.seat_id = t.seat_id AND t.status = 'sold'
    WHERE s.session_id = session_id_param
    GROUP BY s.session_id;
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
        SELECT ROUND(AVG(av.double_value)::numeric, 2)::TEXT
        FROM attribute_values av
        JOIN attributes a ON av.attr_id = a.attr_id
        WHERE a.attr_name = 'rating_imdb'
    );
END;
$$ LANGUAGE plpgsql;