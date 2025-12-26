--
-- PostgreSQL database dump
--

-- Dumped from database version 17.5
-- Dumped by pg_dump version 17.5

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: attribute_types; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.attribute_types (
    type_id integer NOT NULL,
    type_name character varying(100) NOT NULL,
    data_type character varying(50) NOT NULL,
    display_name character varying(100) NOT NULL,
    description text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT attribute_types_data_type_check CHECK (((data_type)::text = ANY ((ARRAY['text'::character varying, 'boolean'::character varying, 'date'::character varying, 'float'::character varying, 'integer'::character varying])::text[])))
);


ALTER TABLE public.attribute_types OWNER TO postgres;

--
-- Name: attribute_types_type_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.attribute_types_type_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attribute_types_type_id_seq OWNER TO postgres;

--
-- Name: attribute_types_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.attribute_types_type_id_seq OWNED BY public.attribute_types.type_id;


--
-- Name: attribute_values; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.attribute_values (
    value_id integer NOT NULL,
    movie_id integer NOT NULL,
    attribute_id integer NOT NULL,
    text_value text,
    boolean_value boolean,
    date_value date,
    float_value double precision,
    integer_value integer,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT only_one_value_type CHECK ((((text_value IS NOT NULL) AND (boolean_value IS NULL) AND (date_value IS NULL) AND (float_value IS NULL) AND (integer_value IS NULL)) OR ((text_value IS NULL) AND (boolean_value IS NOT NULL) AND (date_value IS NULL) AND (float_value IS NULL) AND (integer_value IS NULL)) OR ((text_value IS NULL) AND (boolean_value IS NULL) AND (date_value IS NOT NULL) AND (float_value IS NULL) AND (integer_value IS NULL)) OR ((text_value IS NULL) AND (boolean_value IS NULL) AND (date_value IS NULL) AND (float_value IS NOT NULL) AND (integer_value IS NULL)) OR ((text_value IS NULL) AND (boolean_value IS NULL) AND (date_value IS NULL) AND (float_value IS NULL) AND (integer_value IS NOT NULL))))
);


ALTER TABLE public.attribute_values OWNER TO postgres;

--
-- Name: attribute_values_value_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.attribute_values_value_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attribute_values_value_id_seq OWNER TO postgres;

--
-- Name: attribute_values_value_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.attribute_values_value_id_seq OWNED BY public.attribute_values.value_id;


--
-- Name: attributes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.attributes (
    attribute_id integer NOT NULL,
    type_id integer NOT NULL,
    attribute_name character varying(100) NOT NULL,
    display_name character varying(100) NOT NULL,
    description text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.attributes OWNER TO postgres;

--
-- Name: attributes_attribute_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.attributes_attribute_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attributes_attribute_id_seq OWNER TO postgres;

--
-- Name: attributes_attribute_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.attributes_attribute_id_seq OWNED BY public.attributes.attribute_id;


--
-- Name: movies; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.movies (
    movie_id integer NOT NULL,
    title character varying(255) NOT NULL,
    release_date date,
    duration_minutes integer,
    description text,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.movies OWNER TO postgres;

--
-- Name: marketing_data; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.marketing_data AS
 SELECT m.movie_id,
    m.title AS film,
    att.type_name AS attribute_type,
    a.display_name AS attribute,
        CASE
            WHEN ((att.data_type)::text = 'text'::text) THEN av.text_value
            WHEN ((att.data_type)::text = 'boolean'::text) THEN
            CASE
                WHEN av.boolean_value THEN 'Да'::text
                ELSE 'Нет'::text
            END
            WHEN ((att.data_type)::text = 'date'::text) THEN to_char((av.date_value)::timestamp with time zone, 'DD.MM.YYYY'::text)
            WHEN ((att.data_type)::text = 'float'::text) THEN (av.float_value)::text
            WHEN ((att.data_type)::text = 'integer'::text) THEN (av.integer_value)::text
            ELSE NULL::text
        END AS value
   FROM (((public.movies m
     JOIN public.attribute_values av ON ((m.movie_id = av.movie_id)))
     JOIN public.attributes a ON ((av.attribute_id = a.attribute_id)))
     JOIN public.attribute_types att ON ((a.type_id = att.type_id)))
  WHERE ((att.type_name)::text = ANY ((ARRAY['рецензии'::character varying, 'премия'::character varying, 'важные даты'::character varying])::text[]))
  ORDER BY m.title, att.type_name, a.display_name;


ALTER VIEW public.marketing_data OWNER TO postgres;

--
-- Name: movies_movie_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.movies_movie_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.movies_movie_id_seq OWNER TO postgres;

--
-- Name: movies_movie_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.movies_movie_id_seq OWNED BY public.movies.movie_id;


--
-- Name: operational_tasks; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW public.operational_tasks AS
 SELECT m.movie_id,
    m.title,
    string_agg(
        CASE
            WHEN (av.date_value = CURRENT_DATE) THEN (((at.display_name)::text || ': '::text) ||
            CASE
                WHEN ((att.data_type)::text = 'date'::text) THEN to_char((av.date_value)::timestamp with time zone, 'DD.MM.YYYY'::text)
                WHEN ((att.data_type)::text = 'boolean'::text) THEN
                CASE
                    WHEN av.boolean_value THEN 'Да'::text
                    ELSE 'Нет'::text
                END
                WHEN ((att.data_type)::text = 'float'::text) THEN (av.float_value)::text
                WHEN ((att.data_type)::text = 'integer'::text) THEN (av.integer_value)::text
                ELSE av.text_value
            END)
            ELSE NULL::text
        END, ', '::text) FILTER (WHERE (av.date_value = CURRENT_DATE)) AS today_tasks,
    string_agg(
        CASE
            WHEN (av.date_value = (CURRENT_DATE + '20 days'::interval)) THEN (((at.display_name)::text || ': '::text) ||
            CASE
                WHEN ((att.data_type)::text = 'date'::text) THEN to_char((av.date_value)::timestamp with time zone, 'DD.MM.YYYY'::text)
                WHEN ((att.data_type)::text = 'boolean'::text) THEN
                CASE
                    WHEN av.boolean_value THEN 'Да'::text
                    ELSE 'Нет'::text
                END
                WHEN ((att.data_type)::text = 'float'::text) THEN (av.float_value)::text
                WHEN ((att.data_type)::text = 'integer'::text) THEN (av.integer_value)::text
                ELSE av.text_value
            END)
            ELSE NULL::text
        END, ', '::text) FILTER (WHERE (av.date_value = (CURRENT_DATE + '20 days'::interval))) AS in_20_days_tasks
   FROM ((((public.movies m
     LEFT JOIN public.attribute_values av ON ((m.movie_id = av.movie_id)))
     LEFT JOIN public.attributes a ON ((av.attribute_id = a.attribute_id)))
     LEFT JOIN public.attribute_types att ON ((a.type_id = att.type_id)))
     LEFT JOIN public.attribute_types at ON ((a.type_id = at.type_id)))
  WHERE (((att.type_name)::text = 'служебные даты'::text) AND (av.date_value = ANY (ARRAY[(CURRENT_DATE)::timestamp without time zone, (CURRENT_DATE + '20 days'::interval)])))
  GROUP BY m.movie_id, m.title;


ALTER VIEW public.operational_tasks OWNER TO postgres;

--
-- Name: attribute_types type_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_types ALTER COLUMN type_id SET DEFAULT nextval('public.attribute_types_type_id_seq'::regclass);


--
-- Name: attribute_values value_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_values ALTER COLUMN value_id SET DEFAULT nextval('public.attribute_values_value_id_seq'::regclass);


--
-- Name: attributes attribute_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attributes ALTER COLUMN attribute_id SET DEFAULT nextval('public.attributes_attribute_id_seq'::regclass);


--
-- Name: movies movie_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.movies ALTER COLUMN movie_id SET DEFAULT nextval('public.movies_movie_id_seq'::regclass);


--
-- Data for Name: attribute_types; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.attribute_types (type_id, type_name, data_type, display_name, description, created_at) FROM stdin;
1	рецензии	text	Рецензии	Текстовые рецензии на фильм	2025-06-16 16:14:19.634319
2	премия	boolean	Премии	Награды, полученные фильмом	2025-06-16 16:14:19.634319
3	важные даты	date	Важные даты	Значимые даты, связанные с фильмом	2025-06-16 16:14:19.634319
4	служебные даты	date	Служебные даты	Даты для внутреннего планирования	2025-06-16 16:14:19.634319
\.


--
-- Data for Name: attribute_values; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.attribute_values (value_id, movie_id, attribute_id, text_value, boolean_value, date_value, float_value, integer_value, created_at, updated_at) FROM stdin;
1	1	1	Визуально потрясающий и эмоционально мощный фильм.	\N	\N	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
2	1	3	\N	t	\N	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
3	1	5	\N	\N	2014-10-26	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
4	1	6	\N	\N	2014-11-06	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
5	1	7	\N	\N	2023-11-01	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
6	1	8	\N	\N	2023-10-15	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
7	2	2	Шедевр кинематографа, который должен посмотреть каждый.	\N	\N	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
8	2	4	\N	t	\N	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
9	2	5	\N	\N	1972-03-15	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
10	2	7	\N	\N	2023-11-10	\N	\N	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
\.


--
-- Data for Name: attributes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.attributes (attribute_id, type_id, attribute_name, display_name, description, created_at) FROM stdin;
1	1	critics_review	Рецензия критиков	Оценка фильма профессиональными критиками	2025-06-16 16:14:19.634319
2	1	academy_review	Отзыв киноакадемии	Официальный отзыв киноакадемии	2025-06-16 16:14:19.634319
3	2	oscar	Оскар	Фильм получил премию Оскар	2025-06-16 16:14:19.634319
4	2	nika	Ника	Фильм получил премию Ника	2025-06-16 16:14:19.634319
5	3	world_premiere	Мировая премьера	Дата мировой премьеры фильма	2025-06-16 16:14:19.634319
6	3	russia_premiere	Премьера в РФ	Дата премьеры фильма в России	2025-06-16 16:14:19.634319
7	4	tickets_sale_start	Начало продажи билетов	Дата начала продажи билетов	2025-06-16 16:14:19.634319
8	4	tv_ad_start	Запуск рекламы на ТВ	Дата начала рекламной кампании на телевидении	2025-06-16 16:14:19.634319
\.


--
-- Data for Name: movies; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.movies (movie_id, title, release_date, duration_minutes, description, created_at, updated_at) FROM stdin;
1	Интерстеллар	2014-11-06	169	Фантастический фильм о путешествии через червоточину	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
2	Крестный отец	1972-03-24	175	Классика гангстерского кино	2025-06-16 16:14:19.634319	2025-06-16 16:14:19.634319
\.


--
-- Name: attribute_types_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.attribute_types_type_id_seq', 4, true);


--
-- Name: attribute_values_value_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.attribute_values_value_id_seq', 10, true);


--
-- Name: attributes_attribute_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.attributes_attribute_id_seq', 8, true);


--
-- Name: movies_movie_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.movies_movie_id_seq', 2, true);


--
-- Name: attribute_types attribute_types_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_types
    ADD CONSTRAINT attribute_types_pkey PRIMARY KEY (type_id);


--
-- Name: attribute_types attribute_types_type_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_types
    ADD CONSTRAINT attribute_types_type_name_key UNIQUE (type_name);


--
-- Name: attribute_values attribute_values_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_values
    ADD CONSTRAINT attribute_values_pkey PRIMARY KEY (value_id);


--
-- Name: attributes attributes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attributes
    ADD CONSTRAINT attributes_pkey PRIMARY KEY (attribute_id);


--
-- Name: movies movies_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.movies
    ADD CONSTRAINT movies_pkey PRIMARY KEY (movie_id);


--
-- Name: attributes unique_attribute_name_per_type; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attributes
    ADD CONSTRAINT unique_attribute_name_per_type UNIQUE (type_id, attribute_name);


--
-- Name: idx_attribute_values_attribute_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_attribute_values_attribute_id ON public.attribute_values USING btree (attribute_id);


--
-- Name: idx_attribute_values_boolean_value; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_attribute_values_boolean_value ON public.attribute_values USING btree (boolean_value);


--
-- Name: idx_attribute_values_date_value; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_attribute_values_date_value ON public.attribute_values USING btree (date_value);


--
-- Name: idx_attribute_values_movie_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_attribute_values_movie_id ON public.attribute_values USING btree (movie_id);


--
-- Name: idx_attributes_type_id; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_attributes_type_id ON public.attributes USING btree (type_id);


--
-- Name: attribute_values attribute_values_attribute_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_values
    ADD CONSTRAINT attribute_values_attribute_id_fkey FOREIGN KEY (attribute_id) REFERENCES public.attributes(attribute_id) ON DELETE CASCADE;


--
-- Name: attribute_values attribute_values_movie_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attribute_values
    ADD CONSTRAINT attribute_values_movie_id_fkey FOREIGN KEY (movie_id) REFERENCES public.movies(movie_id) ON DELETE CASCADE;


--
-- Name: attributes attributes_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.attributes
    ADD CONSTRAINT attributes_type_id_fkey FOREIGN KEY (type_id) REFERENCES public.attribute_types(type_id);


--
-- PostgreSQL database dump complete
--

