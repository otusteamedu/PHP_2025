CREATE TABLE app_user
(
    id    SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE movie
(
    id               SERIAL PRIMARY KEY,
    title            VARCHAR(255) NOT NULL,
    description      VARCHAR,
    release_date     DATE         NOT NULL,
    duration_minutes INT          NOT NULL
);

CREATE TABLE hall
(
    id     SERIAL PRIMARY KEY,
    title  VARCHAR(255) NOT NULL,
    scheme JSONB        NOT NULL
);

CREATE TABLE seat
(
    hall_id     INT         NOT NULL REFERENCES hall (id) ON DELETE CASCADE,
    row_number  INT         NOT NULL,
    seat_number INT         NOT NULL,
    seat_type   VARCHAR(50) NOT NULL,
    PRIMARY KEY (hall_id, row_number, seat_number)
);

CREATE TABLE session
(
    id            SERIAL PRIMARY KEY,
    hall_id       INT            NOT NULL REFERENCES hall (id) ON DELETE CASCADE,
    movie_id      INT            NOT NULL REFERENCES movie (id) ON DELETE CASCADE,
    start_time    TIMESTAMP      NOT NULL,
    end_time      TIMESTAMP      NOT NULL,
    price_regular DECIMAL(10, 2) NOT NULL,
    price_vip     DECIMAL(10, 2) NOT NULL
);

CREATE TABLE ticket
(
    user_id       INT NOT NULL REFERENCES app_user (id) ON DELETE CASCADE,
    session_id    INT NOT NULL REFERENCES session (id) ON DELETE CASCADE,
    hall_id       INT NOT NULL,
    row_number    INT NOT NULL,
    seat_number   INT NOT NULL,
    purchase_time TIMESTAMP DEFAULT NOW(),
    PRIMARY KEY (session_id, hall_id, row_number, seat_number),
    FOREIGN KEY (hall_id, row_number, seat_number)
        REFERENCES seat (hall_id, row_number, seat_number) ON DELETE CASCADE
);


CREATE INDEX idx_session_movie_id ON session (movie_id);
CREATE INDEX idx_ticket_session_id ON ticket (session_id);
CREATE INDEX idx_ticket_purchase_time ON ticket (purchase_time);
CREATE INDEX idx_session_price_vip ON session (price_vip);


SELECT m.title, SUM(CASE s.price_vip WHEN 0 THEN s.price_regular ELSE s.price_vip END) AS total_revenue
FROM ticket t
         JOIN session s ON t.session_id = s.id
         JOIN movie m ON s.movie_id = m.id
GROUP BY m.title
ORDER BY total_revenue DESC
    LIMIT 1;
