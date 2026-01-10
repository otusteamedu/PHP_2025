-- Вставка кинотеатров
INSERT INTO cinema (name, city, address)
VALUES ('Монитор СБС', 'Краснодар', 'ул. Уральская, 79/1, РЦ «Семь звёзд», 2 этаж'),
       ('Монитор Красная Площадь', 'Краснодар', 'ул. Дзержинского, 100, МЦ «Красная Площадь», 2-3 этажи'),
       ('Каро 8 Галерея', 'Краснодар', 'ул. Володи Головатого, 313, ТРЦ «Галерея Краснодар»');


-- Вставка клиентов
DO $$
    DECLARE
i INT;
BEGIN
FOR i IN 1..10
    LOOP
        INSERT INTO clients (name, email, phone)
        VALUES (CONCAT('client_ ', i), CONCAT('client_', i, '@test.com'), generate_random_phone_number());
END LOOP;
END $$;


-- Вставка залов
DO $$
    DECLARE
i INT;
BEGIN
FOR i IN 1..10
    LOOP
        INSERT INTO halls (name, category, cinema_id)
        VALUES (
            (array['Синий', 'Зеленый', 'Красный', 'Желтый', 'Черный'])[floor(random() * 5 + 1)],
            (enum_range(null::enum_hall_category))[floor(random() * 6 + 1)],
            (SELECT id FROM cinema ORDER BY RANDOM() LIMIT 1)
        );
END LOOP;
END $$;


-- Вставка фильмов
DO $$
    DECLARE
i INT;
BEGIN
FOR i IN 1..10
    LOOP
        INSERT INTO films (
            is_active, sort, title, actors,
            description, release, genre,
            limitation, show_period_from, show_period_to
        )
        VALUES (
            random() > 0.5, generate_random_int_by_min_and_max(100, 2000), CONCAT('film_', i), generate_random_string_by_int(100),
            generate_random_string_by_int(200), generate_random_int_by_min_and_max(1980, 2025), generate_random_string_by_int(10),
            (enum_range(null::enum_film_limitation))[floor(random() * 5 + 1)], generate_random_timestamp(), generate_random_timestamp()
       );
END LOOP;
END $$;


-- Вставка заказов
DO $$
    DECLARE
i INT;
BEGIN
FOR i IN 1..100
    LOOP
        INSERT INTO orders (client_id, created_at, updated_at, paid_at, status)
        VALUES ((SELECT id FROM clients ORDER BY RANDOM() LIMIT 1), generate_random_timestamp(), generate_random_timestamp(), generate_random_timestamp(), (enum_range(null::enum_order_status))[floor(random() * 3 + 1)]);
END LOOP;
END $$;


-- Вставка сессий
DO $$
    DECLARE
i INT;
BEGIN
FOR i IN 1..100
    LOOP
        INSERT INTO sessions (film_id, hall_id, created_at)
        VALUES ((SELECT id FROM films ORDER BY RANDOM() LIMIT 1), (SELECT id FROM halls ORDER BY RANDOM() LIMIT 1), generate_random_timestamp());
END LOOP;
END $$;


-- Вставка категории мест
DO $$
    DECLARE
i INT;
BEGIN
FOR i IN 1..100
    LOOP
        INSERT INTO place_categories (name, type)
        VALUES (SUBSTRING(MD5(RANDOM()::TEXT), 10, 15), (enum_range(null::enum_place_type))[floor(random() * 5 + 1)]);
END LOOP;
END $$;


-- Вставка мест
DO $$
    DECLARE
i INT;
BEGIN
FOR i IN 1..300
    LOOP
        INSERT INTO places (place_category_id, hall_id, row, number)
        VALUES (
            (SELECT id FROM place_categories ORDER BY RANDOM() LIMIT 1), (SELECT id FROM halls ORDER BY RANDOM() LIMIT 1),
            generate_random_int_by_min_and_max(1, 30), generate_random_int_by_min_and_max(1, 500)
        );
END LOOP;
END $$;


-- Вставка прайс-лист
DO $$
    DECLARE
i INT;
BEGIN
FOR i IN 1..150
    LOOP
        INSERT INTO price_list (session_id, place_category_id, price)
        VALUES (
            (SELECT id FROM sessions ORDER BY RANDOM() LIMIT 1),
            (SELECT id FROM place_categories ORDER BY RANDOM() LIMIT 1),
            ROUND(generate_random_int_by_min_and_max(200, 1000), 2)
        );
END LOOP;
END $$;


-- Вставка билетов
DO $$
    DECLARE
i INT;
BEGIN
FOR i IN 1..300
    LOOP
        INSERT INTO tickets (order_id, session_id, place_id, price)
        VALUES (
            (SELECT id FROM orders ORDER BY RANDOM() LIMIT 1),
            (SELECT id FROM sessions ORDER BY RANDOM() LIMIT 1),
            (SELECT id FROM places ORDER BY RANDOM() LIMIT 1),
            ROUND(generate_random_int_by_min_and_max(200, 1000), 2)
        );
END LOOP;
END $$;
