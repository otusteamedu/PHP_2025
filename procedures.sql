DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `fill_films`()
BEGIN
    DECLARE counter INT DEFAULT 1;
    WHILE counter <= 1000 DO
        INSERT INTO films (name) VALUES (
            CONCAT('Фильм ', counter)
        );
        SET counter = counter + 1;
    END WHILE;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `fill_films_attrs`()
BEGIN
    DECLARE counter INT DEFAULT 1;
    WHILE counter <= (SELECT COUNT(id) count FROM films) DO
    
    	INSERT INTO `films_attrs`(`film_id`, `attr_id`) VALUES (
			counter,
			FLOOR(1 + RAND() * 1000)
		);

    	SET counter = counter + 1;
        
    END WHILE;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `fill_films_attrs_values`()
BEGIN
    DECLARE counter INT DEFAULT 1;
    WHILE counter <= (SELECT COUNT(id) count FROM films) DO
    
    	INSERT INTO `films_attrs_values`(`attr_type_id`, `text`, `date`, `boolean`, `float`) VALUES (
            '1',
            counter,
            NOW(),
            true,
            '5.01'
        );

        SET counter = counter + 1;
    END WHILE;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `fill_halls`()
BEGIN
    DECLARE counter INT DEFAULT 1;
    WHILE counter <= 1000 DO
        INSERT INTO halls (name,seating) VALUES (
            CONCAT('Зал ', counter),
            FLOOR(1 + RAND() * 100)
        );
        SET counter = counter + 1;
    END WHILE;
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `fill_halls_seats`()
BEGIN

    DECLARE hall INT DEFAULT 1;
	
	DECLARE seat INT DEFAULT 1;
    
    WHILE hall <= (SELECT COUNT(id) count FROM `halls`) DO
        
		SET seat = 1;
		
        WHILE seat <= (SELECT halls.seating FROM `halls` WHERE `id` = hall ) DO
        
            INSERT INTO `halls_seats`(`hall_id`, `seat_id`, `row_id`) VALUES (
                hall,
                seat,
                FLOOR(1 + RAND() * 10));
           	SET seat = seat + 1;   
            
        END WHILE;
        
        SET hall = hall + 1;
        
    END WHILE;
    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `fill_tikets`()
BEGIN

    DECLARE timetable_seatings_id INT DEFAULT 1;
	
    DECLARE user_id INT DEFAULT 0;
    
    WHILE timetable_seatings_id <= 1000000 DO

		IF user_id >= 1000 THEN
			SET user_id = 1;
		ELSE 
			SET user_id = user_id + 1;
		END IF;

		
        
		INSERT INTO `tikets`(`timetable_seatings_id`, `user_id`, `price`, `buydate`) VALUES (
			timetable_seatings_id,
			user_id,
			FLOOR(RAND() * (1000 - 300 + 1)) + 300,
			DATE_ADD(NOW(), INTERVAL FLOOR(RAND() * 15 - 7) DAY)
		);
        
        SET timetable_seatings_id = timetable_seatings_id + 1;
        
    END WHILE;
    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `fill_timetable`()
BEGIN

    DECLARE hall INT DEFAULT 1;
	
	DECLARE film INT DEFAULT 1;
    
    WHILE hall <= ((SELECT COUNT(id) count FROM `halls`)) DO
        
		SET film = 1;
		
        WHILE film <= ((SELECT COUNT(id) count FROM `films`)) DO
        
        	INSERT INTO `timetable`(`time`, `hall_id`, `film_id`) VALUES (
                (UNIX_TIMESTAMP(NOW()) + film + hall),
                hall,
                film
            ); 
			
			SET film = film + 1;
            
        END WHILE;
        
        SET hall = hall + 1;
        
    END WHILE;
    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `fill_timetable_seatings`()
BEGIN

    DECLARE timetable_id INT DEFAULT 1;
	
	DECLARE hall_seat_id INT DEFAULT 1;
    
    WHILE timetable_id <= 1000 DO
        
		SET hall_seat_id = 1;
		
        WHILE hall_seat_id <= 1000 DO
        
        	INSERT INTO `timetable_seatings`(`timetable_id`, `hall_seat_id`, `price`) VALUES (
			timetable_id,
			hall_seat_id,
			FLOOR(RAND() * (1000 - 300 + 1)) + 300
			);
			
			SET hall_seat_id = hall_seat_id + 1;
            
        END WHILE;
        
        SET timetable_id = timetable_id + 1;
        
    END WHILE;
    
END$$
DELIMITER ;

DELIMITER $$
CREATE DEFINER=`root`@`%` PROCEDURE `fill_users`()
BEGIN
    DECLARE counter INT DEFAULT 1;
    WHILE counter <= 1000 DO
        INSERT INTO `users`(`name`, `email`, `phone`) VALUES (
			CONCAT('Имя ', counter),
			CONCAT('email ', counter),
			CONCAT('телефон ', counter)
        );
        SET counter = counter + 1;
    END WHILE;
END$$
DELIMITER ;
