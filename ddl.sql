CREATE TABLE users
(
    id    SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE movies
(
    id               SERIAL PRIMARY KEY,
    title            VARCHAR(255) NOT NULL,
    description      VARCHAR,
    release_date     DATE         NOT NULL,
    duration_minutes INT          NOT NULL
);

CREATE TABLE halls
(
    id     SERIAL PRIMARY KEY,
    title  VARCHAR(255) NOT NULL,
    scheme JSONB        NOT NULL
);

CREATE TABLE seats
(
    hall_id     INT         NOT NULL REFERENCES halls (id) ON DELETE CASCADE,
    row_number  INT         NOT NULL,
    seat_number INT         NOT NULL,
    seat_type   VARCHAR(50) NOT NULL,
    PRIMARY KEY (hall_id, row_number, seat_number)
);

CREATE TABLE sessions
(
    id            SERIAL PRIMARY KEY,
    hall_id       INT            NOT NULL REFERENCES halls (id) ON DELETE CASCADE,
    movie_id      INT            NOT NULL REFERENCES movies (id) ON DELETE CASCADE,
    start_time    TIMESTAMP      NOT NULL,
    end_time      TIMESTAMP      NOT NULL,
    price_regular DECIMAL(10, 2) NOT NULL,
    price_vip     DECIMAL(10, 2) NOT NULL
);

CREATE TABLE orders
(
    id           SERIAL PRIMARY KEY,
    user_id      INT            NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    order_time   TIMESTAMP DEFAULT NOW(),
    total_amount DECIMAL(10, 2) NOT NULL
);

CREATE TABLE tickets
(
    id            SERIAL PRIMARY KEY,
    order_id      INT            NOT NULL REFERENCES orders (id) ON DELETE CASCADE,
    session_id    INT            NOT NULL REFERENCES sessions (id) ON DELETE CASCADE,
    hall_id       INT            NOT NULL,
    row_number    INT            NOT NULL,
    seat_number   INT            NOT NULL,
    seat_type     VARCHAR(50)    NOT NULL,
    price         DECIMAL(10, 2) NOT NULL,
    purchase_time TIMESTAMP DEFAULT NOW(),
    UNIQUE (session_id, row_number, seat_number)
);

CREATE INDEX idx_tickets_purchase_time ON tickets(purchase_time);
CREATE INDEX idx_sessions_date ON sessions(start_time);
CREATE INDEX idx_seats_hall_row_seat ON seats (hall_id, row_number, seat_number);
CREATE INDEX idx_tickets_session_id ON tickets(session_id);
