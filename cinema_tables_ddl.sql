-- Залы
CREATE TABLE public.hall
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT hall_pk PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Фильмы
CREATE TABLE public.movie
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT movie_pk PRIMARY KEY,
    title VARCHAR(100) NOT NULL
);

-- Типы мест
CREATE TABLE public.seat_type
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT seat_type_pk PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    price NUMERIC(10, 2) NOT NULL
);

-- Места
CREATE TABLE public.seat
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT seat_pk PRIMARY KEY,
    hall_id INTEGER NOT NULL CONSTRAINT seat_hall_id_fk REFERENCES public.hall,
    row_num INTEGER NOT NULL CONSTRAINT row_num CHECK (row_num > 0),
    seat_num INTEGER NOT NULL CONSTRAINT seat_num CHECK (seat_num > 0),
    seat_type_id INTEGER NOT NULL CONSTRAINT seat_seat_type_id_fk REFERENCES public.seat_type
);

-- Показы
CREATE TABLE public.showtime
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT showtime_pk PRIMARY KEY,
    hall_id INTEGER NOT NULL CONSTRAINT showtime_hall_id_fk REFERENCES public.hall,
    movie_id INTEGER NOT NULL CONSTRAINT showtime_movie_id_fk REFERENCES public.movie,
    time TIMESTAMP NOT NULL
);

CREATE UNIQUE INDEX showtime_hall_id_time_uindex ON public.showtime (hall_id, time);

-- Покупатели
CREATE TABLE public.customer
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT customer_pk PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(12) NOT NULL
);

CREATE UNIQUE INDEX customer_email_uindex ON public.customer (email);

-- Заказы
CREATE TABLE public."order"
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT order_pk PRIMARY KEY,
    customer_id INTEGER NOT NULL CONSTRAINT order_customer_id_fk REFERENCES public.customer,
    showtime_id INTEGER NOT NULL CONSTRAINT order_showtime_id_fk REFERENCES public.showtime,
    seat_id INTEGER NOT NULL CONSTRAINT order_seat_id_fk REFERENCES public.seat,
    order_time TIMESTAMP NOT NULL,
    price NUMERIC(10, 2) NOT NULL CONSTRAINT price CHECK (price >= 0)
);

CREATE UNIQUE INDEX order_showtime_id_seat_id_uindex ON public."order" (showtime_id, seat_id);
