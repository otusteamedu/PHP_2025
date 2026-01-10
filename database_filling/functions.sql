-- Вставка кинотеатров
CREATE OR replace FUNCTION fill_cinema(num INTEGER) RETURNS void AS
$$ DECLARE
    cinema_id INT := (SELECT coalesce( (SELECT id FROM cinema ORDER BY ID DESC LIMIT 1), 1::INTEGER ) AS fetch_1);
    first_value INT := (SELECT CASE WHEN cinema_id = 1 THEN 1 ELSE cinema_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);
BEGIN
    INSERT INTO "cinema" ("id", "name", "city", "address")
    SELECT
        gs.id,
        generate_random_string_by_int(20),
        generate_random_string_by_int(20),
        generate_random_string_by_int(100)
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка клиентов
CREATE OR replace FUNCTION fill_clients(num INTEGER) RETURNS void AS
$$ DECLARE
    client_id INT := (SELECT coalesce( (SELECT id FROM clients ORDER BY ID DESC LIMIT 1), 1::INTEGER ) AS fetch_1);
    first_value INT := (SELECT CASE WHEN client_id = 1 THEN 1 ELSE client_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);
BEGIN
    INSERT INTO "clients" ("id", "name", "email", "phone")
    SELECT
        gs.id,
        CONCAT('client_ ', generate_random_int_by_min_and_max(first_value, last_value)),
        CONCAT('client_', gs.id, '@test.com'),
        generate_random_phone_number()
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка залов
CREATE OR replace FUNCTION fill_halls(num INTEGER) RETURNS void AS
$$ DECLARE
    hall_id INT := (SELECT coalesce( (SELECT id FROM halls ORDER BY ID DESC LIMIT 1), 1::integer ) AS fetch_1);
    first_value INT := (SELECT CASE WHEN hall_id = 1 THEN 1 ELSE hall_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);

    max_cinema_id INT := (SELECT max(id) FROM cinema);
    name_halls CHAR[] := '[0:4]={Синий,Зеленый,Красный,Желтый,Черный}'::CHAR[];
BEGIN
    INSERT INTO "halls" ("id", "name", "category", "cinema_id")
    SELECT
        gs.id,
        name_halls[trunc(random() * 5)::INT],
        (enum_range(NULL::enum_hall_category))[floor(random() * 6 + 1)],
        generate_random_int_by_min_and_max(1, max_cinema_id)
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка фильмов
CREATE OR replace FUNCTION fill_films(num INTEGER) RETURNS void AS
$$ DECLARE
    film_id INT := (SELECT coalesce( (SELECT id FROM films ORDER BY ID DESC LIMIT 1), 1::INTEGER ) AS fetch_1);
    first_value INT := (SELECT CASE WHEN film_id = 1 THEN 1 ELSE film_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);
BEGIN
    INSERT INTO "films" ("id", "is_active", "sort", "title", "actors", "description", "release", "genre", "limitation", "show_period_from", "show_period_to")
    SELECT
        gs.id,
        random() > 0.5,
        generate_random_int_by_min_and_max(100, 2000),
        CONCAT('film_', generate_random_int_by_min_and_max(first_value, last_value)),
        generate_random_string_by_int(100),
        generate_random_string_by_int(200),
        generate_random_int_by_min_and_max(1980, 2025),
        generate_random_string_by_int(10),
        (enum_range(NULL::enum_film_limitation))[floor(random() * 5 + 1)],
        generate_random_timestamp(),
        generate_random_timestamp()
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка заказов
CREATE OR replace FUNCTION fill_orders(num INTEGER) RETURNS void AS
$$ DECLARE
    order_id INT := (SELECT coalesce( (SELECT id FROM orders ORDER BY ID DESC LIMIT 1), 1::INTEGER ) AS fetch_1);
    first_value INT := (SELECT CASE WHEN order_id = 1 THEN 1 ELSE order_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);

    max_client_id INT := (SELECT max(id) FROM clients);
BEGIN
    INSERT INTO "orders" ("id", "client_id", "created_at", "updated_at", "paid_at", "status")
    SELECT
        gs.id,
        generate_random_int_by_min_and_max(1, max_client_id),
        generate_random_timestamp(),
        generate_random_timestamp(),
        generate_random_timestamp(),
        (enum_range(NULL::enum_order_status))[floor(random() * 3 + 1)]
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка сессий
CREATE OR replace FUNCTION fill_sessions(num INTEGER) RETURNS void AS
$$ DECLARE
    session_id INT := (SELECT coalesce( (SELECT id FROM sessions ORDER BY ID DESC LIMIT 1), 1::INTEGER ) AS fetch_1);
    first_value INT := (SELECT CASE WHEN session_id = 1 THEN 1 ELSE session_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);

    max_film_id INT := (SELECT max(id) FROM films);
    max_hall_id INT := (SELECT max(id) FROM halls);
BEGIN
    INSERT INTO "sessions" ("id", "film_id", "hall_id", "created_at")
    SELECT
        gs.id,
        generate_random_int_by_min_and_max(1, max_film_id),
        generate_random_int_by_min_and_max(1, max_hall_id),
        generate_random_timestamp()
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка категории мест
CREATE OR replace FUNCTION fill_place_categories(num INTEGER) RETURNS void AS
$$ DECLARE
    place_category_id INT := (SELECT coalesce( (SELECT id FROM place_categories ORDER BY ID DESC LIMIT 1), 1::INTEGER ) AS FETCH_1);
    first_value INT := (SELECT CASE WHEN place_category_id = 1 THEN 1 ELSE place_category_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);
BEGIN
    INSERT INTO "place_categories" ("id", "name", "type")
    SELECT
        gs.id,
        CONCAT('place_', gs.id, '_', SUBSTRING(MD5(RANDOM()::TEXT), 10, 15)),
        (enum_range(NULL::enum_place_type))[floor(random() * 5 + 1)]
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка мест
CREATE OR replace FUNCTION fill_places(num INTEGER) RETURNS void AS
$$ DECLARE
    place_id INT := (SELECT coalesce( (SELECT id FROM places ORDER BY ID DESC LIMIT 1), 1::INTEGER ) AS fetch_1);
    first_value INT := (SELECT CASE WHEN place_id = 1 THEN 1 ELSE place_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);

    max_place_category_id INT := (SELECT max(id) FROM place_categories);
    max_hall_id INT := (SELECT max(id) FROM halls);
BEGIN
    INSERT INTO "places" ("id", "place_category_id", "hall_id", "row", "number")
    SELECT
        gs.id,
        generate_random_int_by_min_and_max(1, max_place_category_id),
        generate_random_int_by_min_and_max(1, max_hall_id),
        generate_random_int_by_min_and_max(1, 30),
        generate_random_int_by_min_and_max(1, 500)
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка прайс-лист
CREATE OR replace FUNCTION fill_price_list(num INTEGER) RETURNS void AS
$$ DECLARE
    price_list_id INT := (SELECT coalesce( (SELECT id FROM price_list ORDER BY ID DESC LIMIT 1), 1::INTEGER ) AS fetch_1);
    first_value INT := (SELECT CASE WHEN price_list_id = 1 THEN 1 ELSE price_list_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);

    max_session_id INT := (SELECT max(id) FROM sessions);
    max_place_category_id INT := (SELECT max(id) FROM place_categories);
BEGIN
    INSERT INTO "price_list" ("id", "session_id", "place_category_id", "price")
    SELECT
        gs.id,
        generate_random_int_by_min_and_max(1, max_session_id),
        generate_random_int_by_min_and_max(1, max_place_category_id),
        ROUND(generate_random_int_by_min_and_max(200, 1000), 2)
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка билетов
CREATE OR replace FUNCTION fill_tickets(num INTEGER) RETURNS void AS
$$ DECLARE
    ticket_id INT := (SELECT coalesce( (SELECT id FROM tickets ORDER BY ID DESC LIMIT 1), 1::INTEGER ) AS fetch_1);
    first_value INT := (SELECT CASE WHEN ticket_id = 1 THEN 1 ELSE ticket_id + 1 END AS fetch_2);
    last_value INT := (SELECT CASE WHEN first_value = 1 THEN num ELSE (SELECT (first_value + num) - 1 AS SUM) END AS fetch_3);

    max_order_id INT := (SELECT max(id) FROM orders);
    max_session_id INT := (SELECT max(id) FROM sessions);
    max_place_id INT := (SELECT max(id) FROM places);
BEGIN
    INSERT INTO "tickets" ("id", "order_id", "session_id", "place_id", "price")
    SELECT
        gs.id,
        generate_random_int_by_min_and_max(1, max_order_id),
        generate_random_int_by_min_and_max(1, max_session_id),
        generate_random_int_by_min_and_max(1, max_place_id),
        ROUND(generate_random_int_by_min_and_max(200, 1000), 2)
    FROM generate_series(first_value, last_value) AS gs(id);
END;
$$ LANGUAGE plpgsql;


-- Вставка 10000 строк
CREATE OR replace FUNCTION fill_database_10000_rows() RETURNS void AS
$$ BEGIN
    perform fill_cinema(10);
    perform fill_clients(10000);
    perform fill_halls(50);
    perform fill_films(10000);
    perform fill_orders(10000);
    perform fill_sessions(10000);
    perform fill_place_categories(3000);
    perform fill_places(10000);
    perform fill_price_list(10000);
    perform fill_tickets(10000);
END;
$$ LANGUAGE plpgsql;


-- Вставка 10000000 строк
CREATE OR replace FUNCTION fill_database_10000000_rows() RETURNS void AS
$$ BEGIN
    perform fill_cinema(100);
    perform fill_clients(10000000);
    perform fill_halls(500);
    perform fill_films(10000000);
    perform fill_orders(10000000);
    perform fill_sessions(10000000);
    perform fill_place_categories(30000);
    perform fill_places(10000000);
    perform fill_price_list(10000000);
    perform fill_tickets(10000000);
END;
$$ LANGUAGE plpgsql;