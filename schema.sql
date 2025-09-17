BEGIN;

CREATE TABLE test.cinema (
    id          BIGSERIAL PRIMARY KEY,
    name        TEXT        NOT NULL,
    address     TEXT,
    timezone    TEXT        NOT NULL DEFAULT 'UTC',
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE test.hall (
    id          BIGSERIAL PRIMARY KEY,
    cinema_id   BIGINT      NOT NULL REFERENCES test.cinema(id) ON DELETE CASCADE,
    name        TEXT        NOT NULL,
    capacity    INTEGER     NOT NULL CHECK (capacity > 0),
    is_imax     BOOLEAN     NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now(),
    CONSTRAINT uq_hall_name_per_cinema UNIQUE (cinema_id, name)
);
CREATE INDEX ix_hall_cinema_id ON test.hall(cinema_id);

CREATE TABLE test.seat_type (
    id                SMALLSERIAL PRIMARY KEY,
    code              TEXT        NOT NULL UNIQUE,
    name              TEXT        NOT NULL,
    price_multiplier  NUMERIC(6,3) NOT NULL DEFAULT 1.000 CHECK (price_multiplier > 0),
    description       TEXT
);

CREATE TABLE test.seat (
    id            BIGSERIAL PRIMARY KEY,
    hall_id       BIGINT      NOT NULL REFERENCES test.hall(id) ON DELETE CASCADE,
    row_number    INTEGER     NOT NULL CHECK (row_number > 0),
    seat_number   INTEGER     NOT NULL CHECK (seat_number > 0),
    seat_type_id  SMALLINT    NOT NULL REFERENCES test.seat_type(id),
    x             INTEGER,
    y             INTEGER,
    CONSTRAINT uq_seat_per_hall UNIQUE (hall_id, row_number, seat_number)
);
CREATE INDEX ix_seat_hall_id ON test.seat(hall_id);
CREATE INDEX ix_seat_seat_type_id ON test.seat(seat_type_id);

CREATE TABLE test.movie (
    id            BIGSERIAL PRIMARY KEY,
    title         TEXT        NOT NULL,
    release_date  DATE,
    duration_min  SMALLINT    NOT NULL CHECK (duration_min > 0),
    age_rating    TEXT,
    created_at    TIMESTAMPTZ NOT NULL DEFAULT now()
);
CREATE INDEX ix_movie_title ON test.movie(title);

CREATE TABLE test.showtime (
    id          BIGSERIAL PRIMARY KEY,
    hall_id     BIGINT      NOT NULL REFERENCES test.hall(id) ON DELETE RESTRICT,
    movie_id    BIGINT      NOT NULL REFERENCES test.movie(id) ON DELETE RESTRICT,
    start_time  TIMESTAMPTZ NOT NULL,
    end_time    TIMESTAMPTZ NOT NULL,
    base_price  NUMERIC(10,2) NOT NULL CHECK (base_price >= 0),
    language    TEXT,
    format      TEXT,
    CONSTRAINT uq_showtime_hall_start UNIQUE (hall_id, start_time)
);
CREATE INDEX ix_showtime_hall_id ON test.showtime(hall_id);
CREATE INDEX ix_showtime_movie_id ON test.showtime(movie_id);
CREATE INDEX ix_showtime_start_time ON test.showtime(start_time);

CREATE TABLE test.customer (
    id          BIGSERIAL PRIMARY KEY,
    full_name   TEXT        NOT NULL,
    email       TEXT UNIQUE,
    phone       TEXT UNIQUE,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE test.payment (
    id          BIGSERIAL PRIMARY KEY,
    amount      NUMERIC(10,2) NOT NULL CHECK (amount >= 0),
    currency    CHAR(3)       NOT NULL DEFAULT 'RUB',
    method      TEXT          NOT NULL,
    status      TEXT          NOT NULL CHECK (status IN ('pending','paid','failed','refunded','cancelled')),
    paid_at     TIMESTAMPTZ,
    created_at  TIMESTAMPTZ   NOT NULL DEFAULT now()
);
CREATE INDEX ix_payment_status ON test.payment(status);

CREATE TABLE test.ticket (
    id            BIGSERIAL PRIMARY KEY,
    showtime_id   BIGINT      NOT NULL REFERENCES test.showtime(id) ON DELETE CASCADE,
    seat_id       BIGINT      NOT NULL REFERENCES test.seat(id) ON DELETE RESTRICT,
    customer_id   BIGINT      REFERENCES test.customer(id) ON DELETE SET NULL,
    status        TEXT        NOT NULL CHECK (status IN ('reserved','sold','refunded','cancelled')),
    reserved_at   TIMESTAMPTZ NOT NULL DEFAULT now(),
    sold_at       TIMESTAMPTZ,
    refunded_at   TIMESTAMPTZ,
    price_amount  NUMERIC(10,2) NOT NULL CHECK (price_amount >= 0),
    refund_amount NUMERIC(10,2) CHECK (refund_amount >= 0),
    currency      CHAR(3)     NOT NULL DEFAULT 'RUB',
    payment_id    BIGINT      REFERENCES test.payment(id) ON DELETE SET NULL,
    CONSTRAINT chk_refund_le_price CHECK (
        refund_amount IS NULL OR (refund_amount >= 0 AND refund_amount <= price_amount)
    )
);
CREATE INDEX ix_ticket_showtime_id ON test.ticket(showtime_id);
CREATE INDEX ix_ticket_seat_id ON test.ticket(seat_id);
CREATE INDEX ix_ticket_customer_id ON test.ticket(customer_id);
CREATE INDEX ix_ticket_payment_id ON test.ticket(payment_id);
CREATE INDEX ix_ticket_status ON test.ticket(status);

COMMIT;
