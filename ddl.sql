CREATE TABLE `theatre` (
    `theatre_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(200) NOT NULL,
    `location` TEXT
);

CREATE TABLE `room` (
    `room_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `theatre_id` INTEGER NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    FOREIGN KEY (`theatre_id`) REFERENCES `theatre`(`theatre_id`)
);

CREATE TABLE `movie` (
    `movie_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `movie_name` VARCHAR(255) NOT NULL,
    `duration` INTEGER,
    `movie_description` TEXT,
    `release_date` DATE,
    `rating` FLOAT
);

CREATE TABLE `session` (
    `session_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `room_id` INTEGER NOT NULL,
    `movie_id` INTEGER NOT NULL,
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NOT NULL,
    FOREIGN KEY (`room_id`) REFERENCES `room`(`room_id`),
    FOREIGN KEY (`movie_id`) REFERENCES `movie`(`movie_id`)
);

CREATE TABLE `seat_type` (
    `seat_type_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `price` DECIMAL NOT NULL
);

CREATE TABLE `seat` (
    `seat_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `room_id` INTEGER NOT NULL,
    `row_number` INTEGER NOT NULL,
    `seat_number` INTEGER NOT NULL,
    `seat_type_id` INTEGER NOT NULL,
    FOREIGN KEY (`room_id`) REFERENCES `room`(`room_id`),
    FOREIGN KEY (`seat_type_id`) REFERENCES `seat_type`(`seat_type_id`),
    UNIQUE (`room_id`, `row_number`, `seat_number`)
);

CREATE TABLE `user` (
    `user_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) UNIQUE,
    `phone` VARCHAR(25) NOT NULL UNIQUE,
    `registration_date` DATETIME NOT NULL
);

CREATE TABLE `order_status` (
    `status_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `status_name` VARCHAR(100) NOT NULL
);

CREATE TABLE `order` (
    `order_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `user_id` INTEGER NOT NULL,
    `created_at` DATETIME NOT NULL,
    `status` INTEGER NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES user(`user_id`),
    FOREIGN KEY (`status`) REFERENCES order_status(`status_id`)
);

CREATE TABLE `booking` (
    `booking_id` INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `order_id` INTEGER NOT NULL,
    `session_id` INTEGER NOT NULL,
    `seat_id` INTEGER NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `order`(`order_id`) ON DELETE CASCADE,
    FOREIGN KEY (`session_id`) REFERENCES `session`(`session_id`),
    FOREIGN KEY (`seat_id`) REFERENCES `seat`(`seat_id`),
    UNIQUE (`session_id`, `seat_id`)
);