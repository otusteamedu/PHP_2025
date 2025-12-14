-- Фильмы
CREATE TABLE public.movie
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT movie_pk PRIMARY KEY,
    title VARCHAR(150) NOT NULL
);

CREATE UNIQUE INDEX movie_title_uindex ON public.movie (title);

-- Тип аттрибута
CREATE TABLE public.attribute_type
( 
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT attribute_type_pk PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    data_type VARCHAR(20) NOT NULL CHECK (data_type IN ('text', 'date', 'boolean', 'integer', 'decimal'))
);

-- Аттрибут
CREATE TABLE public.attribute
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT attribute_pk PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    attribute_type_id INTEGER NOT NULL CONSTRAINT attribute_attribute_type_id_fk REFERENCES public.attribute_type
);

-- Значение
CREATE TABLE public.attribute_value
(
    id INTEGER GENERATED ALWAYS AS IDENTITY CONSTRAINT attribute_value_pk PRIMARY KEY,
    movie_id INTEGER NOT NULL CONSTRAINT attribute_value_movie_id_fk REFERENCES public.movie,
    attribute_id INTEGER NOT NULL CONSTRAINT attribute_value_attribute_id_fk REFERENCES public.attribute,
    text_value TEXT,
    date_value DATE,
    boolean_value BOOLEAN,
    integer_value INTEGER,
    decimal_value NUMERIC(15, 2)
);

CREATE UNIQUE INDEX attribute_value_movie_id_attribute_id_uindex
    ON public.attribute_value (movie_id, attribute_id);

CREATE INDEX attribute_value_date_value_index 
    ON public.attribute_value (date_value);

CREATE INDEX attribute_value_movie_id_date_value_index 
    ON public.attribute_value (movie_id, date_value) 
    WHERE (date_value IS NOT NULL);

CREATE INDEX attribute_value_attribute_id_index 
    ON public.attribute_value (attribute_id) INCLUDE (text_value, date_value, boolean_value, decimal_value);
