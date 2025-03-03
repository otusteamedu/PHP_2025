-- Создание БД
CREATE DATABASE IF NOT EXISTS cinema;

USE cinema;

CREATE TYPE enum_hall_categories AS ENUM ('IMAX', '3D', 'VIP');
CREATE TYPE enum_place_types AS ENUM ('standard', 'comfort', 'vip');
CREATE TYPE enum_movie_limitations AS ENUM ('0', '16', '18');
CREATE TYPE enum_order_statuses AS ENUM ('new', 'canceled', 'paid');

-- Таблица Пользователи
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    active BOOLEAN NOT NULL DEFAULT 1,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NULL,
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Таблица Залы
CREATE TABLE halls (
    id SERIAL PRIMARY KEY,
    active BOOLEAN NOT NULL DEFAULT 1,
    number INT UNIQUE NOT NULL,
    category enum_hall_categories,
    capacity INT NOT NULL CHECK (capacity > 0),
    open_time_from TIME NOT NULL,
    open_time_to TIME NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Таблица Фильмы
CREATE TABLE movies (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    duration INT NOT NULL,
    limitation enum_movie_limitations,
    release_date DATE NOT NULL
);

-- Таблица Жанры Фильмов
CREATE TABLE genres (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Таблица Жанры Фильмов
CREATE TABLE genre_movie (
    genre_id INT REFERENCES genres(id),
    movie_id INT REFERENCES movies(id),
    PRIMARY KEY (genre_id, movie_id)
);

-- Таблица Сеансы
CREATE TABLE sessions (
    id SERIAL PRIMARY KEY,
    movie_id INT REFERENCES movies(id),
    hall_id INT REFERENCES halls(id),
    price DECIMAL(10, 2) NOT NULL,
    start_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Таблица Посадочные места
CREATE TABLE seats (
    id SERIAL PRIMARY KEY,
    type enum_place_types,
    row_number INT NOT NULL CHECK (row_number > 0),
    place_number INT NOT NULL CHECK (place_number > 0),
    hall_id INT REFERENCES halls(id),
    coefficient NUMERIC(5, 2) DEFAULT 1,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Таблица Билеты
CREATE TABLE tickets (
    id SERIAL PRIMARY KEY,
    session_id INT REFERENCES sessions(id),
    seat_id INT REFERENCES seats(id),
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    CONSTRAINT unique_tickets_session_id_seat_id UNIQUE (session_id, seat_id)
);

-- Таблица Заказы
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    number VARCHAR(255) NOT NULL,
    user_id INT REFERENCES users(id),
    status enum_order_statuses,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);

-- Таблица pivot билеты в заказе
CREATE TABLE order_ticket (
    order_id INT REFERENCES orders(id),
    ticket_id INT REFERENCES tickets(id),
    PRIMARY KEY (order_id, ticket_id)
);

-- Таблица Счета на оплату
CREATE TABLE invoices (
    id SERIAL PRIMARY KEY,
    number VARCHAR(255) NOT NULL UNIQUE,
    amount DECIMAL(10, 2) NOT NULL,
    order_id INT REFERENCES orders(id),
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
);