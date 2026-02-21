SET search_path TO cinema;

DO $$

DECLARE clients_number INT;
DECLARE movies_number INT;
DECLARE movies_begin_date DATE;

BEGIN

    -- rows number is less than 80k by default
    clients_number = 1000;
    movies_number = 15;
    movies_begin_date = date_trunc('month', current_timestamp)::date;

    -- uncomment this block to get about 10M rows in total (query will execute 2-3 minutes)
    -- clients_number = 10000;
    -- movies_number = 1500;
    -- movies_begin_date = date_trunc('year', current_date - interval '7 years')::date;

    DROP MATERIALIZED VIEW IF EXISTS
        ticket_view;

    TRUNCATE TABLE
        client,
        hall,
        hall_type,
        movie,
        "order",
        seat,
        seat_type,
        session,
        session_period,
        session_price,
        ticket
    RESTART IDENTITY CASCADE;

    INSERT INTO hall_type (
        type_name,
        price_modifier,
        seats_number,
        rows_number
    ) VALUES
        ('Стандарт', 1, 112, 8),
        ('Комфорт', 1.5, 60, 6),
        ('Бизнес', 2, 30, 5);

    INSERT INTO hall
        SELECT
            halls.id,
            concat('Зал ', halls.id, ' (', type_name, ')') AS name,
            hall_type.id AS hall_type_id
        FROM generate_series(1, 10) AS halls(id)
        JOIN hall_type ON
            (halls.id BETWEEN 1 AND 6 AND hall_type.type_name = 'Стандарт')
            OR (halls.id BETWEEN 7 AND 9 AND hall_type.type_name = 'Комфорт')
            OR (halls.id = 10 AND hall_type.type_name = 'Бизнес');

    INSERT INTO seat_type (
        type_name,
        hall_type_id,
        price_modifier
    ) VALUES
        ('Передние', 1, 1),
        ('Центральные', 1, 1.3),
        ('Боковые', 1, 1),
        ('Задние', 1, 1.2),
        ('Комфортные', 2, 1),
        ('Ультра-комфортные', 2, 1.3),
        ('Бизнес', 3, 1),
        ('Люкс', 3, 1.5);

    INSERT INTO
        seat
    SELECT
        *,
        row_number() OVER (PARTITION BY hall_id, row_number) AS seat_number
    FROM (
        SELECT
            row_number() OVER () AS id,
            hall_id,
            (array_sample(seat_type_ids, 1))[1] AS seat_type_id,
            ntile(rows_number) OVER (PARTITION BY hall_id) AS row_number
        FROM (
            SELECT
                hall.id AS hall_id,
                hall_type.seats_number AS seats_number,
                hall_type.rows_number AS rows_number,
                array_agg(seat_type.id ORDER BY seat_type.id) AS seat_type_ids
            FROM hall
            JOIN hall_type ON hall.hall_type_id = hall_type.id
            JOIN seat_type ON seat_type.hall_type_id = hall_type.id
            GROUP BY hall_id, seats_number, rows_number
        ) AS halls
        JOIN generate_series(1, seats_number) AS seat ON seat <= seats_number
    ) AS seats
    ORDER BY id;

    INSERT INTO session_period (
        period_name,
        begin_time,
        end_time,
        price_modifier
    ) VALUES
        ('Утро', time '06:00', time '11:59', 1),
        ('День', time '12:00', time '17:59', 1.2),
        ('Вечер', time '18:00', time '23:59', 1.3),
        ('Ночь', time '00:00', time '05:59', 0.9);

    INSERT INTO
        movie
    SELECT
        movies.id,
        'Movie ' || movies.id AS name,
        'Description ' || movies.id AS description,
        random(90, 150) AS duration,
        random(4.5, 9.5) AS rating,
        movies_begin_date + (ntile(movies_number / 5) OVER (ORDER BY movies.id) - 1) * 10 AS release_date
    FROM generate_series(1, movies_number) AS movies(id);

    INSERT INTO
        session
    SELECT
        row_number() OVER (ORDER BY movie_id, start_time) AS id,
        start_time,
        start_time + make_interval(mins => duration) AS end_time,
        movie_id,
        random(1, (SELECT count(*) FROM hall)) AS hall_id,
        session_period.id AS session_period_id
    FROM (
        SELECT
            movie.id AS movie_id,
            duration,
            date_bin(
                '15 minutes',
                release_date + make_interval(
                    days => ntile(10) OVER (PARTITION BY movie.id, days_offset) + days_offset - 1,
                    hours => random(0, 23),
                    mins => random(0, 59)
                ),
                current_date::timestamp
            ) AS start_time
        FROM movie
        CROSS JOIN generate_series(1, 120) AS session
        JOIN unnest(ARRAY[0,10,20]) days_offset ON
            (session <= 60 AND days_offset = 0)
            OR (session > 60 AND session <= 100 AND days_offset = 10)
            OR (session > 100 AND days_offset = 20)
        ORDER BY movie.id, session
    ) AS sessions
    JOIN session_period ON start_time::time BETWEEN begin_time AND end_time
    WHERE start_time < date_trunc('month', current_timestamp) + INTERVAL '1 month';

    INSERT INTO
        session_price
    SELECT
        row_number() OVER (ORDER BY session.id, seat_type.id) AS id,
        round(1000 * hall_type.price_modifier * seat_type.price_modifier * session_period.price_modifier, -1) AS price,
        session.id AS session_id,
        seat_type.id AS seat_type_id
    FROM session
    JOIN session_period ON session.session_period_id = session_period.id
    JOIN hall ON session.hall_id = hall.id
    JOIN hall_type ON hall.hall_type_id = hall_type.id
    JOIN seat_type ON seat_type.hall_type_id = hall_type.id;

    INSERT INTO
        client
    SELECT
        clients.id,
        'email_' || substr(md5(random()::text), 1, 5) || '@test.com' AS email,
        '7' || (array_sample(ARRAY[111, 222, 333], 1))[1] || 1000000 + clients.id AS phone
    FROM generate_series(1, clients_number) AS clients(id);

    CREATE MATERIALIZED VIEW
        ticket_view
    AS SELECT
        row_number() OVER () AS id,
        price,
        dense_rank() OVER (ORDER BY tickets.session_id, seats_group_id) AS order_id,
        session_id,
        seat_id
    FROM (
        SELECT
            session_price.price,
            sessions.session_id,
            busy_seat_id AS seat_id,
            abs(
                row_number() OVER (
                    PARTITION BY sessions.session_id
                    ORDER BY sessions.session_id, busy_seat_id
                ) - busy_seat_id
            ) AS seats_group_id
        FROM (
            SELECT
                session.id AS session_id,
                trim_array(
                    array_agg(seat.id ORDER BY random()),
                    (hall_type.seats_number * random(0.5, 0.7))::int
                ) AS busy_seats_ids
            FROM session
            JOIN hall ON session.hall_id = hall.id
            JOIN hall_type ON hall.hall_type_id = hall_type.id
            JOIN seat ON seat.hall_id = hall.id
            GROUP BY session.id, hall_type.seats_number
        ) AS sessions
        CROSS JOIN unnest(busy_seats_ids) AS busy_seat_id
        JOIN seat ON seat.id = busy_seat_id
        JOIN session_price ON
            session_price.session_id = sessions.session_id
            AND session_price.seat_type_id = seat.seat_type_id
    ) AS tickets
    ORDER BY tickets.session_id, tickets.seat_id;

    INSERT INTO
        "order"
    SELECT
        t.order_id AS id,
        sum(t.price) AS total_price,
        random(1, (SELECT count(*) FROM client)) AS client_id,
        s.start_time - make_interval(days => random(0, 1), hours => random(0, 23), mins => random(0, 59)) AS created_at
    FROM ticket_view t
    JOIN session s ON t.session_id = s.id
    GROUP BY t.order_id, s.start_time
    ORDER BY t.order_id;

    INSERT INTO
        ticket
    SELECT
        *
    FROM ticket_view;

    DROP MATERIALIZED VIEW ticket_view;

END $$;
