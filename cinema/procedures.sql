DELIMITER //
CREATE PROCEDURE create_clients(IN max_num INT)
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= max_num
        DO
            INSERT INTO clients (fio, phone) VALUES ('Тест Тестовый Тестович', '+11121111111');
            SET i = i + 1;
        END WHILE;
END //
DELIMITER ;


DELIMITER //
CREATE PROCEDURE create_films(IN max_num INT)
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= max_num
        DO
            INSERT INTO films(title, genre_id)
            VALUES ('Тестовый фильм', (SELECT genres.id FROM genres ORDER BY RAND() LIMIT 1));
            SET i = i + 1;
        END WHILE;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE create_screenings(IN max_num INT)
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= max_num
        DO
            INSERT INTO screenings(hall_id, film_id, base_price, datetime) VALUES
                ((SELECT halls.id FROM halls ORDER BY RAND() LIMIT 1),
                 (SELECT films.id FROM films ORDER BY RAND() LIMIT 1),
                 RAND()*(20-10)+10,NOW());
            SET i = i + 1;
        END WHILE;
END //
DELIMITER ;


DELIMITER //
CREATE PROCEDURE create_tickets(IN max_num INT)
BEGIN
    DECLARE i INT DEFAULT 1;
    WHILE i <= max_num
        DO
            INSERT INTO tickets(screening_id, client_id, line,place,price,date_of_purchase) VALUES
                (
                    (SELECT screenings.id FROM screenings ORDER BY RAND() LIMIT 1),
                    (SELECT clients.id FROM clients ORDER BY RAND() LIMIT 1),
                    RAND()*(3-1)+1,
                    RAND()*(10-1)+1,
                    RAND()*(20-10)+10,
                    NOW()
                );

            SET i = i + 1;
        END WHILE;
END //
DELIMITER ;

