CREATE TABLE `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `bonus_card_number` int(10) unsigned zerofill DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-clients-email` (`email`) USING BTREE,
  UNIQUE KEY `idx-clients-bonus_card_number` (`bonus_card_number`) USING BTREE,
  KEY `idx-clients-phone` (`phone`) USING BTREE
);

CREATE TABLE `genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `halls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `movies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` smallint(5) unsigned NOT NULL,
  `production_year` year(4) DEFAULT NULL,
  `age_limit` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `movies_genres` (
  `movie_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL,
  PRIMARY KEY (`movie_id`,`genre_id`),
  KEY `fk-movies_genres-movie_id` (`movie_id`) USING BTREE,
  KEY `fk-movies_genres-genre_id` (`genre_id`) USING BTREE,
  CONSTRAINT `fk-movies_genres-genre_id` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk-movies_genres-movie_id` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE
);

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL,
  `status` enum('new','processing','paid') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-orders-client_id` (`client_id`) USING BTREE,
  KEY `fk-orders-status` (`status`) USING BTREE,
  CONSTRAINT `fk-orders-client_id` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
);

CREATE TABLE `seat_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movie_id` int(11) NOT NULL,
  `hall_id` int(11) NOT NULL,
  `started_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-sessions-unique` (`movie_id`,`hall_id`,`started_at`) USING BTREE,
  KEY `fk-sessions-hall_id` (`hall_id`) USING BTREE,
  KEY `fk-sessions-movie_id` (`movie_id`) USING BTREE,
  KEY `fk-sessions-started_at` (`started_at`) USING BTREE,
  CONSTRAINT `fk-sessions-hall_id` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`),
  CONSTRAINT `fk-sessions-movie_id` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`)
);

CREATE TABLE `seat_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `seat_category_id` int(11) NOT NULL,
  `price` decimal(10,2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-seat_prices-unique` (`session_id`,`seat_category_id`) USING BTREE,
  KEY `fk-seat_prices-session_id` (`session_id`) USING BTREE,
  KEY `fk-seat_prices-seat_category_id` (`seat_category_id`) USING BTREE,
  CONSTRAINT `fk-seat_prices-seat_category_id` FOREIGN KEY (`seat_category_id`) REFERENCES `seat_categories` (`id`),
  CONSTRAINT `fk-seat_prices-session_id` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`)
);

CREATE TABLE `seats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seat_category_id` int(11) NOT NULL,
  `hall_id` int(11) NOT NULL,
  `row` int(10) unsigned NOT NULL,
  `seat_number` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-seats-unique` (`seat_category_id`,`hall_id`,`row`,`seat_number`) USING BTREE,
  KEY `fk-seats-seat_category_id` (`seat_category_id`) USING BTREE,
  KEY `fk-seats-hall_id` (`hall_id`) USING BTREE,
  CONSTRAINT `fk-seats-seat_category_id` FOREIGN KEY (`seat_category_id`) REFERENCES `seat_categories` (`id`),
  CONSTRAINT `fk-seats-hall_id` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`)
);

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `price` decimal(10,2) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk-tickets-unique` (`session_id`,`seat_id`) USING BTREE,
  KEY `fk-tickets-session_id` (`session_id`) USING BTREE,
  KEY `fk-tickets-seat_id` (`seat_id`) USING BTREE,
  KEY `fk-tickets-order_id` (`order_id`) USING BTREE,
  CONSTRAINT `fk-tickets-order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `fk-tickets-seat_id` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`),
  CONSTRAINT `fk-tickets-session_id` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`)
);
