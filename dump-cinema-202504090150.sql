--
-- PostgreSQL database dump
--

-- Dumped from database version 12.22 (Ubuntu 12.22-0ubuntu0.20.04.2)
-- Dumped by pg_dump version 17.0

-- Started on 2025-04-09 01:50:10

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP DATABASE cinema;
--
-- TOC entry 2975 (class 1262 OID 49154)
-- Name: cinema; Type: DATABASE; Schema: -; Owner: -
--

CREATE DATABASE cinema WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'C.UTF-8';


\connect cinema

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 6 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA public;


--
-- TOC entry 2976 (class 0 OID 0)
-- Dependencies: 6
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON SCHEMA public IS 'standard public schema';


SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 202 (class 1259 OID 57353)
-- Name: films; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.films (
    id bigint NOT NULL,
    title character(500),
    distribution_start timestamp without time zone,
    distribution_end timestamp without time zone,
    base_price real DEFAULT 1
);


--
-- TOC entry 2977 (class 0 OID 0)
-- Dependencies: 202
-- Name: TABLE films; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.films IS 'фильмы для проката';


--
-- TOC entry 2978 (class 0 OID 0)
-- Dependencies: 202
-- Name: COLUMN films.base_price; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.films.base_price IS 'базовая цена билета без учёта наценок';


--
-- TOC entry 203 (class 1259 OID 57358)
-- Name: halls; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.halls (
    id bigint NOT NULL,
    title character(200),
    seats_count integer DEFAULT 0,
    price_premium real DEFAULT 1
);


--
-- TOC entry 2979 (class 0 OID 0)
-- Dependencies: 203
-- Name: TABLE halls; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.halls IS 'залы кинотеатра';


--
-- TOC entry 2980 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN halls.title; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.halls.title IS 'название или расположение зала';


--
-- TOC entry 2981 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN halls.seats_count; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.halls.seats_count IS 'количество мест в зале';


--
-- TOC entry 2982 (class 0 OID 0)
-- Dependencies: 203
-- Name: COLUMN halls.price_premium; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.halls.price_premium IS 'надбавка к цене в процентах для данного зала от 0 ( скидка минус 100%) до 1 (без надбавки) и выше';


--
-- TOC entry 204 (class 1259 OID 57363)
-- Name: seats; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.seats (
    id bigint NOT NULL,
    seat_row integer NOT NULL,
    seat_number integer NOT NULL,
    seat_class_id bigint NOT NULL,
    hall_id bigint NOT NULL
);


--
-- TOC entry 2983 (class 0 OID 0)
-- Dependencies: 204
-- Name: TABLE seats; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.seats IS 'места в кинозале';


--
-- TOC entry 2984 (class 0 OID 0)
-- Dependencies: 204
-- Name: COLUMN seats.seat_row; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.seats.seat_row IS 'номер ряда в кинозале';


--
-- TOC entry 2985 (class 0 OID 0)
-- Dependencies: 204
-- Name: COLUMN seats.seat_number; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.seats.seat_number IS 'номер места в ряду в кинозале';


--
-- TOC entry 205 (class 1259 OID 57368)
-- Name: seats_class; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.seats_class (
    id bigint NOT NULL,
    title character(100),
    price_premium real DEFAULT 1
);


--
-- TOC entry 2986 (class 0 OID 0)
-- Dependencies: 205
-- Name: TABLE seats_class; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.seats_class IS 'список классов для разных мест и зон в кинозале';


--
-- TOC entry 2987 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN seats_class.title; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.seats_class.title IS 'название класса комфорта';


--
-- TOC entry 2988 (class 0 OID 0)
-- Dependencies: 205
-- Name: COLUMN seats_class.price_premium; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.seats_class.price_premium IS 'надбавка к цене в процентах для мест этого класса от 0 ( скидка минус 100%) до 1 (без надбавки) и выше';


--
-- TOC entry 206 (class 1259 OID 57373)
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id bigint NOT NULL,
    film_id bigint NOT NULL,
    hall_id bigint NOT NULL,
    price_premium real DEFAULT 1,
    session_start timestamp without time zone NOT NULL,
    session_end timestamp without time zone NOT NULL
);


--
-- TOC entry 2989 (class 0 OID 0)
-- Dependencies: 206
-- Name: TABLE sessions; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.sessions IS 'сеансы фильмов';


--
-- TOC entry 2990 (class 0 OID 0)
-- Dependencies: 206
-- Name: COLUMN sessions.price_premium; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN public.sessions.price_premium IS 'надбавка к цене в процентах для сеансов в разное время дня от 0 ( скидка минус 100%) до 1 (без надбавки) и выше';


--
-- TOC entry 207 (class 1259 OID 57378)
-- Name: tickets; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.tickets (
    id bigint NOT NULL,
    seat_id bigint NOT NULL,
    session_id bigint NOT NULL
);


--
-- TOC entry 2991 (class 0 OID 0)
-- Dependencies: 207
-- Name: TABLE tickets; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE public.tickets IS 'билеты на конкретный сеанс и конкретное место в зале';


--
-- TOC entry 2964 (class 0 OID 57353)
-- Dependencies: 202
-- Data for Name: films; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.films VALUES (1, 'Матрица                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             ', '2025-01-01 10:00:00', '2025-02-01 23:59:00', 150);
INSERT INTO public.films VALUES (2, 'Интерстеллар                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ', '2025-02-05 09:00:00', '2025-03-10 22:00:00', 200);
INSERT INTO public.films VALUES (3, 'Начало                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              ', '2025-01-15 12:00:00', '2025-02-15 23:00:00', 250);
INSERT INTO public.films VALUES (4, 'Аватар                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              ', '2025-03-01 08:00:00', '2025-04-01 23:59:00', 300);
INSERT INTO public.films VALUES (5, 'Дюна                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ', '2025-04-01 10:00:00', '2025-05-01 21:00:00', 250);
INSERT INTO public.films VALUES (6, 'Титаник                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             ', '2025-01-10 10:00:00', '2025-02-10 23:00:00', 100);
INSERT INTO public.films VALUES (7, 'Форрест Гамп                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ', '2025-03-05 09:00:00', '2025-04-05 23:59:00', 50);
INSERT INTO public.films VALUES (8, 'Гарри Поттер                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ', '2025-02-20 11:00:00', '2025-03-20 22:00:00', 150);
INSERT INTO public.films VALUES (9, 'Властелин колец                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     ', '2025-03-10 08:30:00', '2025-04-10 21:30:00', 200);
INSERT INTO public.films VALUES (10, 'Темный рыцарь                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       ', '2025-04-05 09:30:00', '2025-05-05 23:30:00', 100);


--
-- TOC entry 2965 (class 0 OID 57358)
-- Dependencies: 203
-- Data for Name: halls; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.halls VALUES (1, 'Зал 1                                                                                                                                                                                                   ', 100, 1);
INSERT INTO public.halls VALUES (2, 'Зал 2                                                                                                                                                                                                   ', 80, 0.9);
INSERT INTO public.halls VALUES (3, 'Зал 3                                                                                                                                                                                                   ', 120, 1.1);
INSERT INTO public.halls VALUES (4, 'VIP Зал                                                                                                                                                                                                 ', 40, 1.5);
INSERT INTO public.halls VALUES (5, 'Зал IMAX                                                                                                                                                                                                ', 150, 1.6);
INSERT INTO public.halls VALUES (6, 'Малый зал                                                                                                                                                                                               ', 60, 0.8);
INSERT INTO public.halls VALUES (7, 'Зал 4                                                                                                                                                                                                   ', 90, 1);
INSERT INTO public.halls VALUES (8, 'Зал 5                                                                                                                                                                                                   ', 110, 1);
INSERT INTO public.halls VALUES (9, 'Зал 6                                                                                                                                                                                                   ', 95, 0.95);
INSERT INTO public.halls VALUES (10, 'Ретро Зал                                                                                                                                                                                               ', 70, 0.85);


--
-- TOC entry 2966 (class 0 OID 57363)
-- Dependencies: 204
-- Data for Name: seats; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.seats VALUES (1, 1, 1, 1, 1);
INSERT INTO public.seats VALUES (2, 1, 2, 1, 1);
INSERT INTO public.seats VALUES (3, 1, 3, 2, 1);
INSERT INTO public.seats VALUES (4, 2, 1, 2, 1);
INSERT INTO public.seats VALUES (5, 2, 2, 3, 1);
INSERT INTO public.seats VALUES (6, 2, 3, 3, 1);
INSERT INTO public.seats VALUES (7, 3, 1, 1, 2);
INSERT INTO public.seats VALUES (8, 3, 2, 2, 2);
INSERT INTO public.seats VALUES (9, 3, 3, 2, 2);
INSERT INTO public.seats VALUES (10, 4, 1, 3, 2);


--
-- TOC entry 2967 (class 0 OID 57368)
-- Dependencies: 205
-- Data for Name: seats_class; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.seats_class VALUES (1, 'Стандарт                                                                                            ', 1);
INSERT INTO public.seats_class VALUES (2, 'Комфорт                                                                                             ', 1.2);
INSERT INTO public.seats_class VALUES (3, 'VIP                                                                                                 ', 1.5);


--
-- TOC entry 2968 (class 0 OID 57373)
-- Dependencies: 206
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.sessions VALUES (1, 1, 1, 1, '2025-04-10 10:00:00', '2025-04-10 12:00:00');
INSERT INTO public.sessions VALUES (2, 2, 2, 1.1, '2025-04-10 13:00:00', '2025-04-10 15:30:00');
INSERT INTO public.sessions VALUES (3, 3, 1, 1, '2025-04-10 16:00:00', '2025-04-10 18:00:00');
INSERT INTO public.sessions VALUES (4, 4, 3, 1.2, '2025-04-10 18:30:00', '2025-04-10 21:00:00');
INSERT INTO public.sessions VALUES (5, 5, 3, 1.1, '2025-04-10 21:30:00', '2025-04-11 00:00:00');
INSERT INTO public.sessions VALUES (6, 6, 4, 0.9, '2025-04-11 10:00:00', '2025-04-11 12:30:00');
INSERT INTO public.sessions VALUES (7, 7, 5, 1.4, '2025-04-11 13:00:00', '2025-04-11 15:30:00');
INSERT INTO public.sessions VALUES (8, 8, 6, 0.8, '2025-04-11 16:00:00', '2025-04-11 18:00:00');
INSERT INTO public.sessions VALUES (9, 9, 7, 1, '2025-04-11 18:30:00', '2025-04-11 21:30:00');
INSERT INTO public.sessions VALUES (10, 10, 8, 1, '2025-04-11 22:00:00', '2025-04-12 00:30:00');


--
-- TOC entry 2969 (class 0 OID 57378)
-- Dependencies: 207
-- Data for Name: tickets; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO public.tickets VALUES (1, 1, 1);
INSERT INTO public.tickets VALUES (2, 2, 1);
INSERT INTO public.tickets VALUES (3, 3, 2);
INSERT INTO public.tickets VALUES (4, 4, 2);
INSERT INTO public.tickets VALUES (5, 5, 3);
INSERT INTO public.tickets VALUES (6, 6, 4);
INSERT INTO public.tickets VALUES (7, 7, 5);
INSERT INTO public.tickets VALUES (8, 8, 6);
INSERT INTO public.tickets VALUES (9, 9, 7);
INSERT INTO public.tickets VALUES (10, 10, 8);


--
-- TOC entry 2821 (class 2606 OID 57357)
-- Name: films films_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.films
    ADD CONSTRAINT films_pk PRIMARY KEY (id);


--
-- TOC entry 2823 (class 2606 OID 57362)
-- Name: halls halls_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.halls
    ADD CONSTRAINT halls_pk PRIMARY KEY (id);


--
-- TOC entry 2827 (class 2606 OID 57372)
-- Name: seats_class seats_class_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.seats_class
    ADD CONSTRAINT seats_class_pk PRIMARY KEY (id);


--
-- TOC entry 2825 (class 2606 OID 57367)
-- Name: seats seats_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.seats
    ADD CONSTRAINT seats_pk PRIMARY KEY (id);


--
-- TOC entry 2829 (class 2606 OID 57377)
-- Name: sessions sessions_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pk PRIMARY KEY (id);


--
-- TOC entry 2831 (class 2606 OID 57382)
-- Name: tickets tickets_pk; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_pk PRIMARY KEY (id);


--
-- TOC entry 2832 (class 2606 OID 57405)
-- Name: seats seats_halls_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.seats
    ADD CONSTRAINT seats_halls_fk FOREIGN KEY (hall_id) REFERENCES public.halls(id);


--
-- TOC entry 2833 (class 2606 OID 57410)
-- Name: seats seats_seats_class_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.seats
    ADD CONSTRAINT seats_seats_class_fk FOREIGN KEY (seat_class_id) REFERENCES public.seats_class(id);


--
-- TOC entry 2834 (class 2606 OID 57395)
-- Name: sessions sessions_films_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_films_fk FOREIGN KEY (film_id) REFERENCES public.films(id);


--
-- TOC entry 2835 (class 2606 OID 57400)
-- Name: sessions sessions_halls_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_halls_fk FOREIGN KEY (hall_id) REFERENCES public.halls(id);


--
-- TOC entry 2836 (class 2606 OID 57420)
-- Name: tickets tickets_seats_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_seats_fk FOREIGN KEY (seat_id) REFERENCES public.seats(id);


--
-- TOC entry 2837 (class 2606 OID 57415)
-- Name: tickets tickets_sessions_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.tickets
    ADD CONSTRAINT tickets_sessions_fk FOREIGN KEY (session_id) REFERENCES public.sessions(id);


-- Completed on 2025-04-09 01:50:10

--
-- PostgreSQL database dump complete
--

