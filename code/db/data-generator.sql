-- Очистка таблиц перед заполнением
TRUNCATE TABLE public.sessions, public.customers, public.halls RESTART IDENTITY CASCADE;

-- Функция для генерации одного кастомера с реалистичным именем
CREATE OR REPLACE FUNCTION generate_customer()
RETURNS BIGINT AS $$
DECLARE
    first_names TEXT[] := ARRAY[
        'Александр', 'Дмитрий', 'Максим', 'Сергей', 'Андрей', 'Алексей', 'Артём', 'Илья', 'Кирилл', 'Михаил',
        'Никита', 'Роман', 'Егор', 'Арсений', 'Иван', 'Денис', 'Евгений', 'Даниил', 'Тимофей', 'Владислав',
        'Вячеслав', 'Константин', 'Владимир', 'Павел', 'Руслан', 'Анна', 'Анастасия', 'Мария', 'Дарья', 'Виктория',
        'Полина', 'Елизавета', 'Екатерина', 'Ксения', 'София', 'Александра', 'Алина', 'Арина', 'Вероника', 'Валерия',
        'Ирина', 'Милана', 'Ульяна', 'Юлия', 'Ева', 'Таисия', 'Маргарита', 'Алиса', 'Диана', 'Кристина'
    ];
    last_names TEXT[] := ARRAY[
        'Иванов', 'Смирнов', 'Кузнецов', 'Попов', 'Васильев', 'Петров', 'Соколов', 'Михайлов', 'Новиков', 'Фёдоров',
        'Морозов', 'Волков', 'Алексеев', 'Лебедев', 'Семёнов', 'Егоров', 'Павлов', 'Козлов', 'Степанов', 'Николаев',
        'Орлов', 'Андреев', 'Макаров', 'Никитин', 'Захаров', 'Зайцев', 'Соловьёв', 'Борисов', 'Яковлев', 'Григорьев',
        'Романов', 'Воробьёв', 'Сергеев', 'Кузьмин', 'Фролов', 'Александров', 'Дмитриев', 'Королёв', 'Гусев', 'Киселёв',
        'Ильин', 'Максимов', 'Поляков', 'Сорокин', 'Виноградов', 'Ковалёв', 'Белов', 'Медведев', 'Антонов', 'Тарасов'
    ];
    random_name TEXT;
    random_phone TEXT;
    new_customer_id BIGINT;
BEGIN
    random_name := first_names[1 + floor(random() * array_length(first_names, 1))::int] || ' ' || last_names[1 + floor(random() * array_length(last_names, 1))::int];

    random_phone := (
        SELECT string_agg(floor(random() * 10)::int::text, '')
        FROM generate_series(1, 10)
    );
    INSERT INTO public.customers (name, phone)
    VALUES (random_name, random_phone)
    RETURNING id INTO new_customer_id;
    RETURN new_customer_id;
END;
$$ LANGUAGE plpgsql;

-- Заполнение таблицы halls
INSERT INTO public.halls (title, rows, seats_per_row, rate) VALUES
('Красный зал', 10, 10, 1.0),
('Синий зал', 10, 12, 1.1),
('VIP-зал', 5, 10, 2.5),
('Зеленый зал', 10, 11, 1.0),
('Зал 3D', 9, 10, 1.5),
('Желтый зал', 8, 10, 1.0),
('Фиолетовый зал', 12, 15, 1.2),
('Малый зал', 6, 8, 0.9),
('Большой зал', 15, 20, 1.3),
('Зал Комфорт', 7, 10, 1.8),
('Семейный зал', 8, 12, 1.1),
('Зал IMAX', 10, 18, 2.0),
('Ретро-зал', 9, 9, 1.0),
('Детский зал', 7, 10, 0.8),
('Премьерный зал', 11, 14, 1.6);

-- Функция для создания сеанса с проверкой на занятость зала
CREATE OR REPLACE FUNCTION create_session(
    p_film_id BIGINT,
    p_hall_id INT,
    p_session_time TIMESTAMP,
    p_price DECIMAL
)
RETURNS INT AS $$
DECLARE
    new_session_id INT;
    hall_is_busy INT;
BEGIN
    SELECT 1 INTO hall_is_busy
    FROM public.sessions s
    JOIN public.session_hall sh ON s.id = sh.session_id
    WHERE sh.hall_id = p_hall_id AND s.date = p_session_time;

    IF hall_is_busy = 1 THEN
        RAISE EXCEPTION 'Зал % уже занят в %', p_hall_id, p_session_time;
    END IF;

    INSERT INTO public.sessions (film_id, date, price)
    VALUES (p_film_id, p_session_time, p_price)
    RETURNING id INTO new_session_id;

    INSERT INTO public.session_hall (session_id, hall_id)
    VALUES (new_session_id, p_hall_id);

    RETURN new_session_id;
END;
$$ LANGUAGE plpgsql;

-- Функция для автоматического заполнения расписания
CREATE OR REPLACE FUNCTION populate_schedule(days_to_fill INT)
RETURNS VOID AS $$
DECLARE
    day_offset INT;
    hall_record RECORD;
    random_film_id BIGINT;
    session_hour INT;
    session_timestamp TIMESTAMP;
    price DECIMAL;
BEGIN
    FOR day_offset IN 0..(days_to_fill - 1) LOOP
        FOR hall_record IN SELECT * FROM public.halls LOOP
            FOREACH session_hour IN ARRAY ARRAY[15, 17, 19, 21] LOOP
                SELECT id INTO random_film_id FROM public.films ORDER BY random() LIMIT 1;
                price := 12 + hall_record.rate;
                session_timestamp := (CURRENT_DATE + day_offset) + (session_hour * INTERVAL '1 hour');
                BEGIN
                    PERFORM create_session(random_film_id, hall_record.id, session_timestamp, price);
                EXCEPTION WHEN OTHERS THEN
                    RAISE NOTICE 'Слот в зале % на % уже занят. Пропускаем.', hall_record.id, session_timestamp;
                END;
            END LOOP;
        END LOOP;
    END LOOP;
END;
$$ LANGUAGE plpgsql;

-- Функция для заполнения билетов на сеансы
CREATE OR REPLACE FUNCTION populate_tickets_for_sessions()
RETURNS VOID AS $$
DECLARE
    session_record RECORD;
    hall_rows INT;
    hall_seats_per_row INT;
    total_places INT;
    tickets_to_generate INT;
    generated_tickets INT;
    customer_id BIGINT;
    tickets_for_customer INT;
    new_ticket_id BIGINT;
    taken_seats_set TEXT[];
    shuffled_rows INT[];
    row_num INT;
    found_row BOOLEAN;
    available_seats_in_row INT[];
    seat_num INT;
    shuffled_available_seats INT[];
    k INT;
BEGIN
    FOR session_record IN
        SELECT
            s.id AS session_id,
            h.rows,
            h.seats_per_row
        FROM public.sessions s
        JOIN public.session_hall sh ON s.id = sh.session_id
        JOIN public.halls h ON sh.hall_id = h.id
        WHERE NOT EXISTS (SELECT 1 FROM public.tickets t WHERE t.session_id = s.id)
    LOOP
        hall_rows := session_record.rows;
        hall_seats_per_row := session_record.seats_per_row;
        total_places := hall_rows * hall_seats_per_row;
        tickets_to_generate := floor(total_places * (random() * 0.25 + 0.6)); -- от 60% до 85%
        generated_tickets := 0;
        taken_seats_set := '{}';

        WHILE generated_tickets < tickets_to_generate LOOP
            customer_id := generate_customer();
            tickets_for_customer := floor(random() * 4 + 2); -- от 2 до 5 билетов

            IF generated_tickets + tickets_for_customer > tickets_to_generate THEN
                tickets_for_customer := tickets_to_generate - generated_tickets;
            END IF;

            IF tickets_for_customer <= 0 THEN
                CONTINUE;
            END IF;

            found_row := FALSE;
            SELECT array_agg(r) INTO shuffled_rows FROM (SELECT generate_series(1, hall_rows) AS r ORDER BY random()) as rows;

            FOREACH row_num IN ARRAY shuffled_rows LOOP
                available_seats_in_row := '{}';
                FOR seat_num IN 1..hall_seats_per_row LOOP
                    IF NOT ((row_num || ',' || seat_num) = ANY(taken_seats_set)) THEN
                        available_seats_in_row := array_append(available_seats_in_row, seat_num);
                    END IF;
                END LOOP;

                IF array_length(available_seats_in_row, 1) >= tickets_for_customer THEN
                    SELECT array_agg(s) INTO shuffled_available_seats FROM (SELECT unnest(available_seats_in_row) s ORDER BY random()) as seats;

                    FOR k IN 1..tickets_for_customer LOOP
                        seat_num := shuffled_available_seats[k];

                        INSERT INTO public.tickets (session_id, row_number, seat_number)
                        VALUES (session_record.session_id, row_num, seat_num)
                        RETURNING id INTO new_ticket_id;

                        INSERT INTO public.customer_tickets (customer_id, ticket_id)
                        VALUES (customer_id, new_ticket_id);

                        taken_seats_set := array_append(taken_seats_set, row_num || ',' || seat_num);
                    END LOOP;

                    generated_tickets := generated_tickets + tickets_for_customer;
                    found_row := TRUE;
                    EXIT;
                END IF;
            END LOOP;

            IF NOT found_row THEN
                DECLARE
                    all_available_seats_str TEXT[] := '{}';
                    seats_to_take_str TEXT[];
                    r INT; s INT;
                    seat_str TEXT;
                    seat_parts TEXT[];
                BEGIN
                    FOR r IN 1..hall_rows LOOP
                        FOR s IN 1..hall_seats_per_row LOOP
                             IF NOT ((r || ',' || s) = ANY(taken_seats_set)) THEN
                                all_available_seats_str := array_append(all_available_seats_str, r || ',' || s);
                             END IF;
                        END LOOP;
                    END LOOP;

                    IF array_length(all_available_seats_str, 1) >= tickets_for_customer THEN
                        SELECT array_agg(seat) INTO seats_to_take_str FROM (
                            SELECT unnest(all_available_seats_str) AS seat ORDER BY random() LIMIT tickets_for_customer
                        ) AS shuffled;

                        FOREACH seat_str IN ARRAY seats_to_take_str LOOP
                            seat_parts := string_to_array(seat_str, ',');
                            r := seat_parts[1]::INT;
                            s := seat_parts[2]::INT;

                            INSERT INTO public.tickets (session_id, row_number, seat_number)
                            VALUES (session_record.session_id, r, s)
                            RETURNING id INTO new_ticket_id;

                            INSERT INTO public.customer_tickets (customer_id, ticket_id)
                            VALUES (customer_id, new_ticket_id);

                            taken_seats_set := array_append(taken_seats_set, seat_str);
                        END LOOP;
                        generated_tickets := generated_tickets + tickets_for_customer;
                    END IF;
                END;
            END IF;
        END LOOP;
    END LOOP;
END;
$$ LANGUAGE plpgsql;


