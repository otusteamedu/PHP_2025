--
-- PostgreSQL database dump
--

\restrict lL1Ogw47xw8l7prBvWQ7NiYvQNQvKrZbKKGsgu4C5dXkHY6wWcvJvWSM3yWD4YP

-- Dumped from database version 13.23
-- Dumped by pg_dump version 13.23

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: attributes; Type: TABLE; Schema: public; Owner: AK
--

CREATE TABLE public.attributes (
    id integer NOT NULL,
    title character varying
);


ALTER TABLE public.attributes OWNER TO "AK";

--
-- Name: attributes_types; Type: TABLE; Schema: public; Owner: AK
--

CREATE TABLE public.attributes_types (
    id integer NOT NULL,
    title character varying
);


ALTER TABLE public.attributes_types OWNER TO "AK";

--
-- Name: films; Type: TABLE; Schema: public; Owner: AK
--

CREATE TABLE public.films (
    title character varying,
    id integer NOT NULL
);


ALTER TABLE public.films OWNER TO "AK";

--
-- Name: values; Type: TABLE; Schema: public; Owner: AK
--

CREATE TABLE public."values" (
    id integer NOT NULL,
    type_id integer,
    attribute_id integer,
    film_id bigint,
    value_numeric float4,
    value_date date,
    value_boolean boolean,
    value_text text
);


ALTER TABLE public."values" OWNER TO "AK";

--
-- Name: about_films; Type: VIEW; Schema: public; Owner: AK
--

CREATE VIEW public.about_films AS
 SELECT f.title AS film_title,
    at.title AS attribute_type_title,
    a.title AS attribute_title,
    COALESCE(v.value_text, v.value_numeric::text, v.value_date::text, v.value_boolean::text) AS value
   FROM public."values" v
     JOIN public.films f ON v.film_id = f.id
     JOIN public.attributes_types at ON v.type_id = at.id
     JOIN public.attributes a ON v.attribute_id = a.id
  ORDER BY f.id;


ALTER TABLE public.about_films OWNER TO "AK";

--
-- Name: about_films_json; Type: VIEW; Schema: public; Owner: AK
--

CREATE VIEW public.about_films_json AS
 SELECT f.title AS film_title,
    jsonb_object_agg(a.title,
        CASE
            WHEN v.value_numeric IS NOT NULL THEN to_jsonb(v.value_numeric)
            WHEN v.value_date IS NOT NULL THEN to_jsonb(v.value_date)
            WHEN v.value_boolean IS NOT NULL THEN to_jsonb(v.value_boolean)
            WHEN v.value_text IS NOT NULL THEN to_jsonb(v.value_text)
            ELSE 'null'::jsonb
        END) AS attributes
   FROM public."values" v
     JOIN public.films f ON v.film_id = f.id
     JOIN public.attributes a ON v.attribute_id = a.id
  GROUP BY f.id, f.title;


ALTER TABLE public.about_films_json OWNER TO "AK";

--
-- Name: attributes_id_seq; Type: SEQUENCE; Schema: public; Owner: AK
--

ALTER TABLE public.attributes ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: attributes_types_id_seq; Type: SEQUENCE; Schema: public; Owner: AK
--

ALTER TABLE public.attributes_types ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.attributes_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: attributes_values_id_seq; Type: SEQUENCE; Schema: public; Owner: AK
--

ALTER TABLE public."values" ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.attributes_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: films_id_seq; Type: SEQUENCE; Schema: public; Owner: AK
--

ALTER TABLE public.films ALTER COLUMN id ADD GENERATED ALWAYS AS IDENTITY (
    SEQUENCE NAME public.films_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1
);


--
-- Name: service_dates; Type: VIEW; Schema: public; Owner: AK
--

CREATE VIEW public.service_dates AS
 SELECT f.title,
    string_agg(CASE WHEN v.value_date = CURRENT_DATE THEN a.title END, ', ') AS today,
    string_agg(CASE WHEN v.value_date > CURRENT_DATE AND v.value_date <= (CURRENT_DATE + 20) THEN a.title END, ', ') AS period
   FROM public."values" v
     JOIN public.attributes a ON v.attribute_id = a.id
     JOIN public.films f ON f.id = v.film_id
  WHERE v.value_date IS NOT NULL
  GROUP BY f.id, f.title
  ORDER BY f.title;


ALTER TABLE public.service_dates OWNER TO "AK";

--
-- Name: workd; Type: VIEW; Schema: public; Owner: AK
--

CREATE VIEW public.workd AS
 WITH dates AS (
         SELECT v.id,
            v.value_date AS value,
            a.title,
            v.film_id
           FROM public."values" v
             JOIN public.attributes_types t ON v.type_id = t.id
             LEFT JOIN public.attributes a ON a.id = v.attribute_id
          WHERE t.title = 'date' AND v.id IN (
              SELECT v2.id
              FROM public."values" v2
              JOIN public.attributes a_1 ON v2.attribute_id = a_1.id
              WHERE a_1.title = ANY (ARRAY['начало продаж', 'начало рекламы'])
          )
    )
 SELECT f.title AS film,
    d.title AS action,
    d.value AS date
   FROM dates d
     JOIN public.films f ON f.id = d.film_id
  WHERE d.value >= CURRENT_DATE AND d.value <= (CURRENT_DATE + 20);


ALTER TABLE public.workd OWNER TO "AK";

--
-- Data for Name: attributes; Type: TABLE DATA; Schema: public; Owner: AK
--

COPY public.attributes (id, title) FROM stdin;
1	рецензии
2	премия
3	премьера
4	премьера в регионе
5	начало продаж
6	начало рекламы
7	отзыв
8	оскар
9	золотой глобус
\.


--
-- Data for Name: attributes_types; Type: TABLE DATA; Schema: public; Owner: AK
--

COPY public.attributes_types (id, title) FROM stdin;
1	varchar
2	integer
3	boolean
4	json
5	text
6	date
7	image
\.


--
-- Data for Name: films; Type: TABLE DATA; Schema: public; Owner: AK
--

COPY public.films (title, id) FROM stdin;
Побег из Шоушенка	1
Крёстный отец	2
\.


--
-- Data for Name: values; Type: TABLE DATA; Schema: public; Owner: AK
--

INSERT INTO public.values ( type_id, attribute_id, film_id, value_text) VALUES ( 5, 7, 1, 'По версии IMDb Считается одним из величайших фильмов в истории кино');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_text) VALUES ( 5, 1, 1, 'Критики и зрители отмечают сильный сценарий, режиссуру и актерскую игру');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_boolean) VALUES ( 3, 8, 2, true);
INSERT INTO public.values ( type_id, attribute_id, film_id, value_boolean) VALUES ( 3, 8, 1, false);
INSERT INTO public.values ( type_id, attribute_id, film_id, value_boolean) VALUES ( 3, 9, 1, false);
INSERT INTO public.values ( type_id, attribute_id, film_id, value_boolean) VALUES ( 3, 9, 2, true);
INSERT INTO public.values ( type_id, attribute_id, film_id, value_text) VALUES ( 5, 1, 2, 'Считается шедевром мирового кинематографа. Хвалят режиссуру Фрэнсиса Форда Копполы и актерскую игру, особенно Марлона Брандо');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_date) VALUES ( 6, 4, 2, '1972-05-14');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_date) VALUES ( 6, 6, 1, '2025-12-16');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_date) VALUES ( 6, 4, 1, '1994-07-23');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_date) VALUES ( 6, 3, 2, '1972-04-24');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_date) VALUES ( 6, 3, 1, '1994-07-23');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_date) VALUES ( 6, 5, 1, '2025-12-15');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_date) VALUES ( 6, 6, 2, '2025-12-21');
INSERT INTO public.values ( type_id, attribute_id, film_id, value_date) VALUES ( 6, 5, 2, '2025-12-22');


--
-- Name: attributes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: AK
--

SELECT pg_catalog.setval('public.attributes_id_seq', 9, true);


--
-- Name: attributes_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: AK
--

SELECT pg_catalog.setval('public.attributes_types_id_seq', 7, true);


--
-- Name: attributes_values_id_seq; Type: SEQUENCE SET; Schema: public; Owner: AK
--

SELECT pg_catalog.setval('public.attributes_values_id_seq', 15, true);


--
-- Name: films_id_seq; Type: SEQUENCE SET; Schema: public; Owner: AK
--

SELECT pg_catalog.setval('public.films_id_seq', 15, true);


--
-- Name: attributes attributes_pk; Type: CONSTRAINT; Schema: public; Owner: AK
--

ALTER TABLE ONLY public.attributes
    ADD CONSTRAINT attributes_pk UNIQUE (id);


--
-- Name: attributes_types attributes_types_pk; Type: CONSTRAINT; Schema: public; Owner: AK
--

ALTER TABLE ONLY public.attributes_types
    ADD CONSTRAINT attributes_types_pk UNIQUE (id);


--
-- Name: films films_pk; Type: CONSTRAINT; Schema: public; Owner: AK
--

ALTER TABLE ONLY public.films
    ADD CONSTRAINT films_pk UNIQUE (id);


--
-- Name: values attributes___fk; Type: FK CONSTRAINT; Schema: public; Owner: AK
--

ALTER TABLE ONLY public."values"
    ADD CONSTRAINT attributes___fk FOREIGN KEY (attribute_id) REFERENCES public.attributes(id);


--
-- Name: values films___fk; Type: FK CONSTRAINT; Schema: public; Owner: AK
--

ALTER TABLE ONLY public."values"
    ADD CONSTRAINT films___fk FOREIGN KEY (film_id) REFERENCES public.films(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: values types___fk; Type: FK CONSTRAINT; Schema: public; Owner: AK
--

ALTER TABLE ONLY public."values"
    ADD CONSTRAINT types___fk FOREIGN KEY (type_id) REFERENCES public.attributes_types(id);


--
-- PostgreSQL database dump complete
--

\unrestrict lL1Ogw47xw8l7prBvWQ7NiYvQNQvKrZbKKGsgu4C5dXkHY6wWcvJvWSM3yWD4YP
