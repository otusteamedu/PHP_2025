CREATE TABLE films
(
    id          BIGINT AUTO_INCREMENT
        PRIMARY KEY,
    title       VARCHAR(500) NOT NULL,
    description TEXT         NULL,
    actors      TEXT         NULL
);

CREATE TABLE sessions
(
    id    INT AUTO_INCREMENT
        PRIMARY KEY,
    film  BIGINT         NULL,
    date  DATETIME       NOT NULL,
    price DECIMAL(10, 2) NULL,
    CONSTRAINT sessions_films_id_fk
        FOREIGN KEY (film) REFERENCES films (id)
);

CREATE TABLE halls
(
    id     INT AUTO_INCREMENT
        PRIMARY KEY,
    places INT            DEFAULT 0    NOT NULL,
    rate   DECIMAL(10, 2) DEFAULT 0.00 NOT NULL,
    title  VARCHAR(50)                 NULL
);

CREATE TABLE session_hall
(
    session_id INT NOT NULL,
    hall_id    INT NOT NULL,
    PRIMARY KEY (session_id, hall_id),
    CONSTRAINT fk_session_hall_session
        FOREIGN KEY (session_id) REFERENCES sessions (id)
            ON DELETE CASCADE,
    CONSTRAINT fk_session_hall_hall
        FOREIGN KEY (hall_id) REFERENCES halls (id)
            ON DELETE CASCADE
);

CREATE TABLE customers
(
    id    INT AUTO_INCREMENT
        PRIMARY KEY,
    name  VARCHAR(255) NOT NULL,
    phone VARCHAR(50)  NULL
);

CREATE TABLE tickets
(
    id         INT AUTO_INCREMENT
        PRIMARY KEY,
    session_id INT NOT NULL,
    seat       INT NOT NULL,
    CONSTRAINT fk_tickets_session
        FOREIGN KEY (session_id) REFERENCES sessions (id)
            ON DELETE CASCADE
);

CREATE TABLE customer_tickets
(
    customer_id INT NOT NULL,
    ticket_id   INT NOT NULL,
    PRIMARY KEY (customer_id, ticket_id),
    CONSTRAINT uq_ticket_id
        UNIQUE (ticket_id),
    CONSTRAINT fk_customer_tickets_customer
        FOREIGN KEY (customer_id) REFERENCES customers (id)
            ON DELETE CASCADE,
    CONSTRAINT fk_customer_tickets_ticket
        FOREIGN KEY (ticket_id) REFERENCES tickets (id)
            ON DELETE CASCADE
);