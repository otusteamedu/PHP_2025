-- DROP SCHEMA public;

CREATE SCHEMA public AUTHORIZATION pg_database_owner;

-- DROP SEQUENCE customers_id_seq;

CREATE SEQUENCE customers_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE films_id_seq;

CREATE SEQUENCE films_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE halls_id_seq;

CREATE SEQUENCE halls_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE orders_id_seq;

CREATE SEQUENCE orders_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE seats_id_seq;

CREATE SEQUENCE seats_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE sessions_id_seq;

CREATE SEQUENCE sessions_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE tickets_id_seq;

CREATE SEQUENCE tickets_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 9223372036854775807
    START 1
	CACHE 1
	NO CYCLE;
-- public.films определение

-- Drop table

-- DROP TABLE films;

CREATE TABLE films
(
    id     int8 GENERATED ALWAYS AS IDENTITY ( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
    "name" varchar                                                                                                              NOT NULL,
    CONSTRAINT films_pk PRIMARY KEY (id)
);


-- public.halls определение

-- Drop table

-- DROP TABLE halls;

CREATE TABLE halls
(
    id     int8 GENERATED ALWAYS AS IDENTITY ( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
    "name" varchar                                                                                                              NOT NULL,
    CONSTRAINT halls_pk PRIMARY KEY (id)
);


-- public.users определение

-- Drop table

-- DROP TABLE users;

CREATE TABLE users
(
    id        int8 GENERATED ALWAYS AS IDENTITY ( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
    "name"    varchar                                                                                                              NOT NULL,
    last_name varchar                                                                                                              NOT NULL,
    CONSTRAINT users_pk PRIMARY KEY (id)
);


-- public.seats определение

-- Drop table

-- DROP TABLE seats;

CREATE TABLE seats
(
    id          int8 GENERATED ALWAYS AS IDENTITY ( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
    hall_id     int8                                                                                                                 NOT NULL,
    number_row  int8                                                                                                                 NOT NULL,
    "number"    int8                                                                                                                 NOT NULL,
    coefficient numeric DEFAULT 0                                                                                                    NOT NULL,
    CONSTRAINT seats_pk PRIMARY KEY (id),
    CONSTRAINT seats_halls_fk FOREIGN KEY (hall_id) REFERENCES halls (id) ON DELETE CASCADE ON UPDATE CASCADE
);


-- public.sessions определение

-- Drop table

-- DROP TABLE sessions;

CREATE TABLE sessions
(
    id                   int8 GENERATED ALWAYS AS IDENTITY ( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
    start_at             timestamp                                                                                                            NOT NULL,
    base_price           money                                                                                                                NOT NULL,
    hall_id              int8                                                                                                                 NOT NULL,
    film_id              int8                                                                                                                 NOT NULL,
    children_coefficient numeric DEFAULT 0                                                                                                    NOT NULL,
    CONSTRAINT sessions_pk PRIMARY KEY (id),
    CONSTRAINT sessions_films_fk FOREIGN KEY (film_id) REFERENCES films (id),
    CONSTRAINT sessions_halls_fk FOREIGN KEY (hall_id) REFERENCES halls (id)
);
CREATE INDEX sessions_film_id_idx ON public.sessions USING btree (film_id);


-- public.tickets определение

-- Drop table

-- DROP TABLE tickets;

CREATE TABLE tickets
(
    id          int8 GENERATED ALWAYS AS IDENTITY ( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
    session_id  int8                                                                                                                 NOT NULL,
    seat_id     int8                                                                                                                 NOT NULL,
    final_price numeric                                                                                                              NOT NULL,
    CONSTRAINT tickets_pk PRIMARY KEY (id),
    CONSTRAINT tickets_seats_fk FOREIGN KEY (seat_id) REFERENCES seats (id),
    CONSTRAINT tickets_sessions_fk FOREIGN KEY (session_id) REFERENCES sessions (id)
);
CREATE INDEX tickets_session_id_idx ON public.tickets USING btree (session_id);


-- public.orders определение

-- Drop table

-- DROP TABLE orders;

CREATE TABLE orders
(
    id        int8 GENERATED ALWAYS AS IDENTITY ( INCREMENT BY 1 MINVALUE 1 MAXVALUE 9223372036854775807 START 1 CACHE 1 NO CYCLE) NOT NULL,
    user_id   int8                                                                                                                 NOT NULL,
    ticket_id int8                                                                                                                 NOT NULL,
    CONSTRAINT orders_tickets_fk FOREIGN KEY (ticket_id) REFERENCES tickets (id),
    CONSTRAINT orders_users_fk FOREIGN KEY (user_id) REFERENCES users (id)
);
