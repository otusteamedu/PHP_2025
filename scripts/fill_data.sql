-- Очистка всех таблиц
TRUNCATE TABLE movie_props_value, attribute, type_attribute, ticket, price_list,
    session_movie, schema_room, room, movie, type_place, hall CASCADE;

-- Генерация залов
CREATE OR REPLACE FUNCTION generate_hall(count_hall INT)
    RETURNS void AS $$
DECLARE
    start_val INT;
BEGIN
    DROP SEQUENCE IF EXISTS num_sequence;
    SELECT COALESCE(MAX(id), 0) + 1 INTO start_val FROM hall;

    EXECUTE format('CREATE SEQUENCE num_sequence START %s', start_val);

    INSERT INTO hall (num)
    SELECT nextval('num_sequence')
    FROM generate_series(1, count_hall);

    DROP SEQUENCE IF EXISTS num_sequence;
END;
$$ LANGUAGE plpgsql;

-- Генерация комнат в залах
CREATE OR REPLACE FUNCTION generate_rooms(rows_hall INT DEFAULT 2)
    RETURNS void AS $$
BEGIN
    DECLARE
        hall_ids INT[] := ARRAY(SELECT id FROM hall ORDER BY id);
    BEGIN
        FOR i IN 1..array_length(hall_ids, 1) LOOP
                FOR row_num IN 1..rows_hall LOOP
                        INSERT INTO room (num, hall_id, effect_type)
                        VALUES (row_num, hall_ids[i], (SELECT 2 + FLOOR(random() * 2)::int) || 'D');
                    END LOOP;
            END LOOP;
    END;
END;
$$ LANGUAGE plpgsql;

-- Генерация типов мест
CREATE OR REPLACE FUNCTION generate_type()
    RETURNS void AS $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM type_place WHERE name = 'Обычное') THEN
        INSERT INTO type_place (name) VALUES ('Обычное')
        ON CONFLICT (id) DO UPDATE SET name = EXCLUDED.name;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM type_place WHERE name = 'VIP') THEN
        INSERT INTO type_place (name) VALUES ('VIP')
        ON CONFLICT (id) DO UPDATE SET name = EXCLUDED.name;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM type_place WHERE name = 'Премьер') THEN
        INSERT INTO type_place (name) VALUES ('Премьер')
        ON CONFLICT (id) DO UPDATE SET name = EXCLUDED.name;
    END IF;
END;
$$ LANGUAGE plpgsql;

-- Генерация фильмов
CREATE OR REPLACE FUNCTION generate_movie(
    count_movie INT
)
    RETURNS void AS $$
BEGIN
INSERT INTO movie(name, release_date, duration, description)
SELECT
    md5(random()::text),
    now() - (random() * (interval '90 days')),
    (random() * 180 + 30) * interval '1 minute',
    md5(random()::text)
FROM generate_series(1, count_movie) AS gs(id);
END;
$$ LANGUAGE plpgsql;

-- Генерация сеансов
CREATE OR REPLACE FUNCTION generate_session_movie(count_session_movie INT)
    RETURNS void AS $$
BEGIN
    INSERT INTO session_movie (room_id, time_start, movie_id)
    WITH
        random_times AS (
            SELECT timestamp '2025-01-01 10:00:00' +
                   (random() * 365)::int * interval '1 day' +
                   (random() * 13)::int * interval '1 hour' as time_start
            FROM generate_series(1, 200)
        ),
        random_rooms AS (
            SELECT id as room_id
            FROM room
            ORDER BY random()
            LIMIT 200
        ),
        random_movies AS (
            SELECT id as movie_id
            FROM movie
            ORDER BY random()
            LIMIT 200
        )
    SELECT
        r.room_id,
        t.time_start,
        m.movie_id
    FROM random_times t
             JOIN random_rooms r ON true
             JOIN random_movies m ON true
    LIMIT count_session_movie;
END;
$$ LANGUAGE plpgsql;

-- Генерация цен
CREATE OR REPLACE FUNCTION generate_price_list(count_price_list INT)
    RETURNS void AS $$
BEGIN
    INSERT INTO price_list (type_id, price, session_movie_id)
    WITH
        base_prices AS (
            SELECT
                id as type_id,
                CASE
                    WHEN name = 'Обычное' THEN 300.00 + (random() * 200)::int
                    WHEN name = 'VIP' THEN 600.00 + (random() * 400)::int
                    ELSE 450.00 + (random() * 300)::int
                    END as base_price
            FROM type_place
        ),
        session_types AS (
            SELECT
                sm.id as session_movie_id,
                tp.id as type_id,
                bp.base_price
            FROM session_movie sm
                     CROSS JOIN type_place tp
                     JOIN base_prices bp ON tp.id = bp.type_id
            ORDER BY random()
        )
    SELECT
        type_id,
        base_price as price,
        session_movie_id
    FROM session_types
    LIMIT count_price_list;
END;
$$ LANGUAGE plpgsql;

-- Генерация схем для комнат
CREATE OR REPLACE FUNCTION generate_schema_room()
    RETURNS void AS $$
DECLARE
    room_rec record;
    type_rec record;
BEGIN
    FOR room_rec IN SELECT id FROM room ORDER BY id LOOP
            FOR r IN 1..5 LOOP
                    FOR p IN 1..3 LOOP
                            SELECT id INTO type_rec FROM type_place ORDER BY random() LIMIT 1;
                            INSERT INTO schema_room (row, place, type_id, room_id)
                            VALUES (r, p, type_rec.id, room_rec.id);
                        END LOOP;
                END LOOP;
        END LOOP;
END;
$$ LANGUAGE plpgsql;

-- Генерация схем билетов
CREATE OR REPLACE FUNCTION generate_ticket(count_ticket INT)
    RETURNS VOID AS $$
DECLARE
    random_status TEXT[];
    base_prices NUMERIC[];
BEGIN
    -- Генерируем массив возможных статусов
    random_status := ARRAY['куплен', 'забронирован', 'возвращен', 'использован'];

    -- Заполняем массив базовых цен (пример для простоты)
    base_prices := ARRAY(SELECT DISTINCT pl.price FROM price_list pl);

    -- Выполняем вставку в один проход
    INSERT INTO ticket (session_movie_id, status, actual_price, schema_id, created_at)
    SELECT
        ssm.id AS session_movie_id,
        random_status[(random() * array_length(random_status, 1))::integer + 1],
        (base_prices[(random() * array_length(base_prices, 1))::integer + 1] * (0.9 + random() * 0.2))::NUMERIC(10, 2),
        ssr.id AS schema_id,
        (ssm.time_start - ((random() * 7 * 24 + 1)::INTEGER || ' hours')::INTERVAL)
    FROM session_movie ssm
             JOIN schema_room ssr ON ssm.room_id = ssr.room_id
    WHERE EXISTS (SELECT 1 FROM price_list pl WHERE pl.session_movie_id = ssm.id)
    ORDER BY RANDOM()
    LIMIT count_ticket;
END;
$$ LANGUAGE plpgsql;

-- Вызов функций для генерации данных
SELECT generate_hall(200);
SELECT generate_type();
SELECT generate_rooms(200);
SELECT generate_movie(10000);
SELECT generate_schema_room();
SELECT generate_session_movie(2000000);
SELECT generate_price_list(10000000);
SELECT generate_ticket(9000000);