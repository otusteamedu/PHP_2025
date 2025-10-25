CREATE TABLE clients
(
    id          SERIAL PRIMARY KEY,
    email       VARCHAR(50) UNIQUE NOT NULL,
    phone       VARCHAR(50),
    last_name   VARCHAR(255),
    first_name  VARCHAR(255)       NOT NULL,
    middle_name VARCHAR(255)
);

CREATE TYPE order_status AS ENUM ('new', 'processing', 'paid');

CREATE TABLE orders
(
    id         SERIAL PRIMARY KEY,
    client_id  INT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    paid_at    TIMESTAMP          DEFAULT NULL,
    status     order_status NOT NULL,
    CONSTRAINT "fk-orders-client_id" FOREIGN KEY (client_id) REFERENCES clients (id)
);

CREATE TABLE movies
(
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(255) UNIQUE NOT NULL,
    duration    SMALLINT     NOT NULL,
    age_limit   SMALLINT
);

CREATE TABLE halls
(
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE seat_categories
(
    id   SERIAL PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE sessions
(
    id         SERIAL PRIMARY KEY,
    movie_id   INT       NOT NULL,
    hall_id    INT       NOT NULL,
    started_at TIMESTAMP NOT NULL,
    CONSTRAINT "fk-sessions-hall_id" FOREIGN KEY (hall_id) REFERENCES halls (id),
    CONSTRAINT "fk-sessions-movie_id" FOREIGN KEY (movie_id) REFERENCES movies (id)
);

CREATE TABLE seat_prices
(
    id               SERIAL PRIMARY KEY,
    session_id       INT            NOT NULL,
    seat_category_id INT            NOT NULL,
    price            DECIMAL(10, 2) NOT NULL CHECK (price > 0),
    CONSTRAINT "fk-seat_prices-seat_category_id" FOREIGN KEY (seat_category_id) REFERENCES seat_categories (id),
    CONSTRAINT "fk-seat_prices-session_id" FOREIGN KEY (session_id) REFERENCES sessions (id)
);

CREATE TABLE seats
(
    id               SERIAL PRIMARY KEY,
    seat_category_id INT NOT NULL,
    hall_id          INT NOT NULL,
    row              INT NOT NULL CHECK (row > 0),
    seat_number      INT NOT NULL CHECK (seat_number > 0),
    CONSTRAINT "fk-seats-seat_category_id" FOREIGN KEY (seat_category_id) REFERENCES seat_categories (id),
    CONSTRAINT "fk-seats-hall_id" FOREIGN KEY (hall_id) REFERENCES halls (id)
);

CREATE TABLE tickets
(
    id         SERIAL PRIMARY KEY,
    order_id   INT            NOT NULL,
    session_id INT            NOT NULL,
    seat_id    INT            NOT NULL,
    price      DECIMAL(10, 2) NOT NULL CHECK (price > 0),
    CONSTRAINT "fk-tickets-order_id" FOREIGN KEY (order_id) REFERENCES orders (id),
    CONSTRAINT "fk-tickets-seat_id" FOREIGN KEY (seat_id) REFERENCES seats (id),
    CONSTRAINT "fk-tickets-session_id" FOREIGN KEY (session_id) REFERENCES sessions (id)
);
