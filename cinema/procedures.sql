CREATE OR REPLACE PROCEDURE create_clients(max_num int)AS $$
    DECLARE
     counter INT := 0;
BEGIN
    WHILE counter < max_num LOOP
INSERT INTO clients (fio, phone) VALUES ('Тест Тестовый Тестович', '+11121111111');
counter := counter + 1;
END LOOP;
END
$$ LANGUAGE 'plpgsql';


CREATE OR REPLACE PROCEDURE create_films(max_num int)AS $$
DECLARE
    counter INT := 0;
BEGIN
    WHILE counter < max_num LOOP
            INSERT INTO films(title, genre_id)
            VALUES ('Тестовый фильм', (SELECT genres.id FROM genres ORDER BY random() LIMIT 1));
            counter := counter + 1;
        END LOOP;
END
$$ LANGUAGE 'plpgsql';



CREATE OR REPLACE PROCEDURE create_screenings(max_num int)AS $$
DECLARE
    counter INT := 0;
BEGIN
    WHILE counter < max_num LOOP
            INSERT INTO screenings(hall_id, film_id, base_price, datetime) VALUES
                ((SELECT halls.id FROM halls ORDER BY random() LIMIT 1),
                 (SELECT films.id FROM films ORDER BY random() LIMIT 1),
                 CEILING(RANDOM() * 20),
                 NOW() + interval '1 second'
                );
            counter := counter + 1;
        END LOOP;
END
$$ LANGUAGE 'plpgsql';



CREATE OR REPLACE PROCEDURE create_tickets(max_num int)AS $$
DECLARE
    counter INT := 0;
BEGIN
    WHILE counter < max_num LOOP
            INSERT INTO tickets(screening_id, client_id, line,place,price,date_of_purchase) VALUES
                (
                    (SELECT screenings.id FROM screenings ORDER BY random() LIMIT 1),
                    (SELECT clients.id FROM clients ORDER BY random() LIMIT 1),
                    CEILING(RANDOM() * 3),
                    CEILING(RANDOM() * 10),
                    CEILING(RANDOM() * 20),
                    NOW()
                );
            counter := counter + 1;
        END LOOP;
END
$$ LANGUAGE 'plpgsql';
