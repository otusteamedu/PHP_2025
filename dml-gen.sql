-- PL/pgSQL-функции для генерации тестового контента

-- Фильмы
CREATE OR REPLACE FUNCTION generate_movies(count INT)
RETURNS VOID AS $$
DECLARE
    i INT;
    rnd_title_idx INT;
    rnd_genre_idx INT;
    -- Для генерации использован запрос для Perplexity: "сгенерируй топ100 самых популярных фильма и дай результат в виде списка, элементы которого заключены в одинарные кавычки и разделены друг от друга запятыми; используй названия в фильмов в российском прокате"
    titles TEXT[] := ARRAY[
        'Чебурашка', 'Холоп 2', 'Аватар', 'Холоп', 'Бременские музыканты', 'Человек-паук: Нет пути домой', 'Движение вверх', 'Король Лев', 'Мстители: Финал', 'По щучьему велению', 'Мастер и Маргарита', 'Т-34', 'Последний богатырь: Посланник тьмы', 'Вызов', 'Пираты Карибского моря: Мертвецы не рассказывают сказки', 'Зверополис', 'Веном 2', 'Последний богатырь: Корень зла', 'Тайная жизнь домашних животных', 'Веном', 'Малефисента: Владычица тьмы', 'Джокер', 'Мстители: Война бесконечности', 'Лёд 3', 'Холодное сердце 2', 'Звёздные войны: Пробуждение силы', 'Как приручить дракона 3', 'Пираты Карибского моря: На странных берегах', 'Полицейский с Рублёвки. Новогодний беспредел', 'Последний богатырь', 'Миньоны', 'Хоббит: Битва пяти воинств', 'Сталинград', 'Дэдпул', 'Отряд самоубийц', 'Ледниковый период 4: Континентальный дрейф', 'Анчартед: На картах не значится', 'Шрек навсегда', 'Дюна', 'Мстители: Эра Альтрона', 'Фантастические твари: Преступления Грин-де-Вальда', 'Мадагаскар 3', 'Форсаж 7', 'Викинг', 'Тайная жизнь домашних животных 2', 'Стражи Галактики. Часть 2', 'Лёд 2', 'Хоббит: Пустошь Смауга', 'Лёд', 'Форсаж 8', 'Трансформеры: Эпоха истребления', 'Экипаж', 'Фантастические твари и где они обитают', 'Гадкий я 3', 'Босс-молокосос', 'Ледниковый период 3: Эра динозавров', 'Кот в сапогах', 'Сумерки. Сага: Рассвет — Часть 2', 'Хоббит: Нежданное путешествие', 'Варкрафт', 'Доктор Стрэндж', 'Джуманджи: Зов джунглей', 'Железный человек 3', 'Душа', 'Дэдпул 2', 'Аквамен', 'Стражи Галактики', 'Тор: Рагнарёк', 'Малефисента', 'Трансформеры 3: Тёмная сторона Луны', 'Книга джунглей', 'Джентльмены', 'Аладдин', 'Алиса в Стране чудес', 'Мир юрского периода', 'Человек-паук: Вдали от дома', 'Мстители', 'Конёк-Горбунок', 'Ной', 'Ирония судьбы. Продолжение', 'Форсаж 9', 'Интерстеллар', 'Ёлки 3', 'Аватар: Путь воды', 'Терминатор: Генезис', 'Капитан Марвел', 'Вий', 'Монстры на каникулах 3: Море зовёт', 'Однажды в Голливуде', 'Джуманджи: Новый уровень', 'Три богатыря и Пуп Земли', 'Оно 2', 'Марсианин', 'Мир юрского периода 2', 'Тор 2: Царство тьмы', 'Люди в чёрном 3', 'Гадкий я 2', 'Форсаж: Хоббс и Шоу', 'Мадагаскар 2', 'Черепашки-ниндзя'
    ];
    genres TEXT[] := ARRAY['Фантастика', 'Драма', 'Боевик', 'Криминал', 'Комедия', 'Приключения', 'Фэнтези'];
BEGIN
    FOR i IN 1..count LOOP
        rnd_title_idx := (RANDOM() * array_length(titles, 1))::INT % array_length(titles, 1) + 1;
        rnd_genre_idx := (RANDOM() * array_length(genres, 1))::INT % array_length(genres, 1) + 1;
        INSERT INTO MOVIE (title, description, duration_minutes, genre, rating)
        VALUES (
            titles[rnd_title_idx],
            'Описание фильма ' || i || '. Это описание для фильма #' || i || '.',
            (RANDOM() * 60 + 90)::INT, -- Продолжительность от 90 до 150 минут
            genres[rnd_genre_idx],
            (RANDOM() * 2 + 3)::DECIMAL(3,1) -- Рейтинг от 3.0 до 5.0
        );
    END LOOP;
END;
$$ LANGUAGE plpgsql;

-- Клиенты
CREATE OR REPLACE FUNCTION generate_customers(count INT)
RETURNS VOID AS $$
DECLARE
    i INT;
    first_names TEXT[] := ARRAY[
        'Иван', 'Петр', 'Сергей', 'Александр', 'Дмитрий', 'Андрей', 'Михаил', 'Николай',
        'Владимир', 'Борис', 'Федор', 'Григорий', 'Евгений', 'Виктор', 'Алексей',
        'Анна', 'Мария', 'Елена', 'Ольга', 'Татьяна', 'Наталья', 'Ирина', 'Светлана',
        'Екатерина', 'Юлия', 'Дарья', 'Анастасия', 'Полина', 'Виктория', 'Ксения'
    ];
    last_names TEXT[] := ARRAY[
        'Иванов', 'Петров', 'Сидоров', 'Кузнецов', 'Волков', 'Смирнов', 'Попов', 'Лебедев',
        'Козлов', 'Новиков', 'Морозов', 'Федоров', 'Михайлов', 'Семенов', 'Алексеев',
        'Егоров', 'Павлов', 'Николаев', 'Максимов', 'Орлов', 'Белов', 'Медведев', 'Комаров',
        'Жуков', 'Борисов', 'Сорокин', 'Виноградов', 'Ковалев', 'Севастьянов', 'Козлов'
    ];
    domains TEXT[] := ARRAY['mail.ru', 'gmail.com', 'yandex.ru', 'bk.ru', 'rambler.ru'];
BEGIN
    FOR i IN 1..count LOOP
        INSERT INTO CUSTOMER (first_name, last_name, email, phone)
        VALUES (
            first_names[((RANDOM() * array_length(first_names, 1))::INT % array_length(first_names, 1)) + 1],
            last_names[((RANDOM() * array_length(last_names, 1))::INT % array_length(last_names, 1)) + 1],
            'mail' || i || '@' ||
                  domains[((RANDOM() * array_length(domains, 1))::INT % array_length(domains, 1)) + 1],
            '+79' || LPAD((i)::TEXT, 9, '0')
        );
    END LOOP;
END;
$$ LANGUAGE plpgsql;

-- Залы и их схемы
CREATE OR REPLACE FUNCTION generate_halls_and_layouts(count INT)
RETURNS VOID AS $$
DECLARE
    i INT;
    rows INT;
    seats_in_row INT;
    hall_name VARCHAR(255);
    layout_name VARCHAR(255);
    tmp_random INT;
    layout_id INT;
BEGIN
    FOR i IN 1..count LOOP
        hall_name := 'Зал ' || i || ' - Цвета X';
        rows := (RANDOM() * 16)::INT + 5; -- ср 12.5
        seats_in_row := (RANDOM() * 19)::INT + 12; -- ср 21 => ~260 мест в зале будет в среднем
        tmp_random := (RANDOM() * 11)::INT;
        layout_name := CASE
            WHEN tmp_random < 1 THEN 'VIP зал'
            WHEN tmp_random < 2 THEN 'IMAX зал'
            WHEN tmp_random < 3 THEN '3D зал'
            WHEN tmp_random < 5 THEN 'Детский зал'
            ELSE 'Основной зал'
        END;
        INSERT INTO HALL_LAYOUT (name, layout_config)
        VALUES (
            layout_name || ' (' || i || ')',
            ('{"rows": ' || rows || ', "seats_in_row": ' || seats_in_row ||
             ', "features": ["Бла бла 1", "Бла бла 2", "Бла бла 3"]}')::JSONB
        ) RETURNING id INTO layout_id;

        INSERT INTO HALL (name, capacity, hall_layout_id)
        VALUES (
            hall_name,
            rows * seats_in_row,
            layout_id
        );

        PERFORM generate_seats(layout_id, rows, seats_in_row);
    END LOOP;
END;
$$ LANGUAGE plpgsql;

-- Места
CREATE OR REPLACE FUNCTION generate_seats(layout_id INT, rows_count INT, seats_in_row INT)
RETURNS VOID AS $$
DECLARE
    r INT;
    s INT;
    seat_type VARCHAR(50);
    price_mult DECIMAL(4,2);
BEGIN
    FOR r IN 1..rows_count LOOP
        FOR s IN 1..seats_in_row LOOP
            IF r <= 3 THEN
                seat_type := 'Первые ряды';
                price_mult := 0.70;
            ELSIF r >= rows_count - 2 THEN
                seat_type := 'VIP1';
                price_mult := 1.50;
            ELSIF r = (rows_count / 2) OR r = (rows_count / 2) + 1 THEN
                seat_type := 'VIP2';
                price_mult := 1.50;
            ELSE
                seat_type := 'Стандарт';
                price_mult := 1.00;
            END IF;

            INSERT INTO HALL_SEAT (hall_layout_id, row_number, seat_number, seat_type, price_multiplier)
            VALUES (layout_id, r, s, seat_type, price_mult);
        END LOOP;
    END LOOP;
END;
$$ LANGUAGE plpgsql;

-- Сеансы
CREATE OR REPLACE FUNCTION generate_sessions(days_before INT)
RETURNS VOID AS $$
DECLARE
    i INT;
    current_d DATE;
    start_time TIMESTAMP;
    end_time TIMESTAMP;
    movie_id INT;
    hall_id INT;
    duration INT;
    base_price DECIMAL(10,2);
    gap INT := 10;
BEGIN
    current_d := (NOW() - (INTERVAL '1 day' * (days_before - 1)))::DATE;
    FOR i IN 1..days_before LOOP
        start_time := current_d + TIME '10:00:00';
        WHILE start_time < current_d + TIME '22:00:00' LOOP
            SELECT id INTO movie_id FROM MOVIE ORDER BY RANDOM() LIMIT 1;
            SELECT id INTO hall_id FROM HALL ORDER BY RANDOM() LIMIT 1;
            SELECT duration_minutes INTO duration FROM MOVIE WHERE id = movie_id;
            base_price := (RANDOM() * 500 + 400)::DECIMAL(10,2);
            end_time := start_time + (INTERVAL '1 minute' * duration);
            INSERT INTO SESSION (hall_id, movie_id, start_time, end_time, base_price)
            VALUES (
                hall_id,
                movie_id,
                start_time,
                end_time,
                base_price
            );
            start_time := end_time + (INTERVAL '1 minute' * gap);
        END LOOP;
        current_d := current_d + (INTERVAL '1 day');
    END LOOP;
END;
$$ LANGUAGE plpgsql;

-- Билеты
CREATE OR REPLACE FUNCTION generate_tickets(count INT, days_before INT)
RETURNS VOID AS $$
DECLARE
    i INT;
    sess_id INT;
    seat_id INT;
    cust_id INT;
    selected_price DECIMAL(10,2);
    base_price DECIMAL(10,2);
    seat_multiplier DECIMAL(4,2);
    session_multiplier DECIMAL(4,2);
    start_day DATE;
    start_t TIMESTAMP;
BEGIN
    FOR i IN 1..count LOOP
        start_day := (NOW() - (INTERVAL '1 day' * (RANDOM() * days_before)))::DATE;
        start_t := start_day + (INTERVAL '1 hour' * (RANDOM() * 12 + 10));
        SELECT s.id, s.base_price INTO sess_id, base_price
        FROM SESSION s
        WHERE s.start_time >= start_t AND s.start_time <= start_t + INTERVAL '3 hour'
        ORDER BY RANDOM() LIMIT 1;

        IF sess_id IS NULL THEN
            CONTINUE;
        END IF;

        SELECT hs.id FROM HALL_SEAT hs
        JOIN HALL h ON h.hall_layout_id = hs.hall_layout_id
        JOIN SESSION s ON s.hall_id = h.id
        WHERE s.id = sess_id
        AND hs.id NOT IN (
            SELECT t.hall_seat_id FROM TICKET t WHERE t.session_id = sess_id
        )
        ORDER BY RANDOM() LIMIT 1
        INTO seat_id;

        IF seat_id IS NULL THEN
            CONTINUE;
        END IF;

        SELECT id INTO cust_id FROM CUSTOMER ORDER BY RANDOM() LIMIT 1;
        SELECT price_multiplier INTO seat_multiplier FROM HALL_SEAT WHERE id = seat_id;

        SELECT sp.price_multiplier INTO session_multiplier
        FROM SEAT_PRICING sp
        WHERE sp.session_id = sess_id AND sp.hall_seat_id = seat_id;

        IF session_multiplier IS NULL THEN
            session_multiplier := 1.00;
        END IF;

        selected_price := base_price * seat_multiplier * session_multiplier;
        INSERT INTO TICKET (session_id, hall_seat_id, customer_id, price)
        VALUES (sess_id, seat_id, cust_id, selected_price);
    END LOOP;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION generate_tickets_optimized(count INT, days_before INT)
RETURNS VOID AS $$
BEGIN
    WITH sessions_availability AS (
        SELECT
            s.id AS session_id,
            s.hall_id,
            s.base_price,
            h.hall_layout_id
        FROM SESSION s
        JOIN HALL h ON h.id = s.hall_id
        WHERE s.start_time >= (NOW() - (INTERVAL '1 day' * days_before))
            AND s.start_time <= NOW()
    ),
    sessions_seats AS (
        SELECT
            sa.session_id,
            sa.base_price,
            hs.id AS seat_id,
            hs.price_multiplier AS seat_price_multiplier
        FROM sessions_availability sa
        JOIN HALL_SEAT hs ON hs.hall_layout_id = sa.hall_layout_id
    ),
    available_combinations AS (
        SELECT
            ss.session_id,
            ss.base_price,
            ss.seat_id,
            ss.seat_price_multiplier,
            (RANDOM() * 1000000000)::BIGINT AS random_order
        FROM sessions_seats ss
        WHERE NOT EXISTS (
            SELECT 1
            FROM TICKET t
            WHERE t.session_id = ss.session_id
                AND t.hall_seat_id = ss.seat_id
        )
    ),
    selected_combinations AS (
        SELECT
            ac.session_id,
            ac.seat_id,
            ac.base_price,
            ac.seat_price_multiplier,
            ac.random_order
        FROM available_combinations ac
        ORDER BY ac.random_order
        LIMIT count
    ),
    final_tickets AS (
        SELECT
            sc.session_id,
            sc.seat_id,
            (SELECT c.id FROM CUSTOMER c ORDER BY RANDOM() LIMIT 1) AS customer_id,
            sc.base_price *
            sc.seat_price_multiplier *
            COALESCE(sp.price_multiplier, 1.00) AS final_price
        FROM selected_combinations sc
        LEFT JOIN SEAT_PRICING sp ON sp.session_id = sc.session_id
            AND sp.hall_seat_id = sc.seat_id
    )
    INSERT INTO TICKET (session_id, hall_seat_id, customer_id, price)
    SELECT session_id, seat_id, customer_id, final_price
    FROM final_tickets
    ON CONFLICT (session_id, hall_seat_id) DO NOTHING;
END;
$$ LANGUAGE plpgsql;

-- Спец.цены на места
CREATE OR REPLACE FUNCTION generate_pricings(count INT)
RETURNS VOID AS $$
DECLARE
    i INT;
    sess_id INT;
    seat_id INT;
    mult DECIMAL(4,2);
    existing_count INT;
BEGIN
    FOR i IN 1..count LOOP
        LOOP
            SELECT id INTO sess_id FROM SESSION ORDER BY RANDOM() LIMIT 1;
            SELECT id INTO seat_id FROM HALL_SEAT ORDER BY RANDOM() LIMIT 1;

            SELECT COUNT(*) INTO existing_count
            FROM SEAT_PRICING
            WHERE session_id = sess_id AND hall_seat_id = seat_id;

            EXIT WHEN existing_count = 0;
        END LOOP;

        mult := (RANDOM() * 2 + 0.5)::DECIMAL(4,2);
        INSERT INTO SEAT_PRICING (session_id, hall_seat_id, price_multiplier, comment)
        VALUES (
            sess_id,
            seat_id,
            mult,
            'Специальная цена для места ' || seat_id || ' на сеанс ' || sess_id
        );
    END LOOP;
END;
$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION generate_pricings_optimized(count INT)
RETURNS VOID AS $$
BEGIN
    WITH all_combinations AS (
        SELECT
            s.id AS session_id,
            hs.id AS seat_id,
            (RANDOM() * 1000000000)::BIGINT AS random_order
        FROM SESSION s
        CROSS JOIN HALL_SEAT hs
    ),
    available_combinations AS (
        SELECT
            ac.session_id,
            ac.seat_id,
            ac.random_order
        FROM all_combinations ac
        WHERE NOT EXISTS (
            SELECT 1
            FROM SEAT_PRICING sp
            WHERE sp.session_id = ac.session_id
                AND sp.hall_seat_id = ac.seat_id
        )
    ),
    selected_combinations AS (
        SELECT
            session_id,
            seat_id
        FROM available_combinations
        ORDER BY random_order
        LIMIT count
    ),
    final_pricings AS (
        SELECT
            sc.session_id,
            sc.seat_id,
            (RANDOM() * 2 + 0.5)::DECIMAL(4,2) AS price_multiplier,
            'Специальная цена для места ' || sc.seat_id || ' на сеанс ' || sc.session_id AS comment
        FROM selected_combinations sc
    )
    INSERT INTO SEAT_PRICING (session_id, hall_seat_id, price_multiplier, comment)
    SELECT session_id, seat_id, price_multiplier, comment
    FROM final_pricings
    ON CONFLICT (session_id, hall_seat_id) DO NOTHING;
END;
$$ LANGUAGE plpgsql;

-- Итоговая функция для генерации разом всех тестовых данных (MIN)
CREATE OR REPLACE FUNCTION generate_all(
    movies INT DEFAULT 10,
    customers INT DEFAULT 1000, -- | t / 2
    halls INT DEFAULT 4, -- | мест = h * ~260
    pricings INT DEFAULT 1000, -- | t / 10
    tickets INT DEFAULT 10000, -- | сеансов * мест
    days_before INT DEFAULT 7 -- | сеансов = 6 * d
)
RETURNS VOID AS $$
DECLARE
    start_time TIMESTAMP := clock_timestamp();
BEGIN
    RAISE NOTICE '-- Генерация % фильмов...', movies;
    PERFORM generate_movies(movies);
    RAISE NOTICE 'Время: %', (clock_timestamp() - start_time);
    start_time := clock_timestamp();

    RAISE NOTICE '-- Генерация % клиентов...', customers;
    PERFORM generate_customers(customers);
    RAISE NOTICE 'Время: %', (clock_timestamp() - start_time);
    start_time := clock_timestamp();

    RAISE NOTICE '-- Генерация % залов и их схем...', halls;
    PERFORM generate_halls_and_layouts(halls);
    RAISE NOTICE 'Время: %', (clock_timestamp() - start_time);
    start_time := clock_timestamp();

    RAISE NOTICE '-- Генерация сеансов за % дней...', days_before;
    PERFORM generate_sessions(days_before);
    RAISE NOTICE 'Время: %', (clock_timestamp() - start_time);
    start_time := clock_timestamp();

    RAISE NOTICE 'Генерация % спец.цен на места...', pricings;
    PERFORM generate_pricings_optimized(pricings);
    RAISE NOTICE 'Время: %', (clock_timestamp() - start_time);
    start_time := clock_timestamp();

    RAISE NOTICE 'Генерация % билетов...', tickets;
    PERFORM generate_tickets_optimized(tickets, days_before);
    RAISE NOTICE 'Время: %', (clock_timestamp() - start_time);
    start_time := clock_timestamp();

    RAISE NOTICE '== Тестовые данные сгенерированы успешно!';
END;
$$ LANGUAGE plpgsql;

-- Итоговая функция для генерации разом всех тестовых данных (MAX)
CREATE OR REPLACE FUNCTION generate_all2()
RETURNS VOID AS $$
BEGIN
    PERFORM generate_all(10000, 1000000, 40, 1000000, 10000000, 5000);
END;
$$ LANGUAGE plpgsql;
