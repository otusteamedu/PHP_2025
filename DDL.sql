-- DROP SCHEMA public;

CREATE SCHEMA public AUTHORIZATION pg_database_owner;

-- DROP SEQUENCE attribute_id_seq;

CREATE SEQUENCE attribute_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 2147483647
    START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE attribute_types_id_seq;

CREATE SEQUENCE attribute_types_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 2147483647
    START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE entity_id_seq;

CREATE SEQUENCE entity_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 2147483647
    START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE value_id_seq;

CREATE SEQUENCE value_id_seq
    INCREMENT BY 1
    MINVALUE 1
    MAXVALUE 2147483647
    START 1
	CACHE 1
	NO CYCLE;
-- public.attribute_types определение

-- Drop table

-- DROP TABLE attribute_types;

CREATE TABLE attribute_types
(
    id     serial4 NOT NULL,
    "type" varchar NOT NULL,
    "name" varchar NOT NULL,
    CONSTRAINT attribute_types_pk PRIMARY KEY (id)
);


-- public.films определение

-- Drop table

-- DROP TABLE films;

CREATE TABLE films
(
    id     int4 DEFAULT nextval('entity_id_seq'::regclass) NOT NULL,
    "name" varchar(255)                                    NOT NULL,
    CONSTRAINT entity_pk PRIMARY KEY (id)
);


-- public."attributes" определение

-- Drop table

-- DROP TABLE "attributes";

CREATE TABLE "attributes"
(
    id      int4 DEFAULT nextval('attribute_id_seq'::regclass) NOT NULL,
    "name"  varchar(255)                                       NOT NULL,
    type_id int4                                               NOT NULL,
    CONSTRAINT attribute_pk PRIMARY KEY (id),
    CONSTRAINT attributes_attribute_types_fk FOREIGN KEY (type_id) REFERENCES attribute_types (id) ON DELETE CASCADE
);


-- public.attribute_values определение

-- Drop table

-- DROP TABLE attribute_values;

CREATE TABLE attribute_values
(
    id          int4 DEFAULT nextval('value_id_seq'::regclass) NOT NULL,
    film_id     int4                                           NOT NULL,
    attibute_id int4                                           NOT NULL,
    value_text  text NULL,
    value_date  timestamp NULL,
    value_bool  bool NULL,
    value_float float4 NULL,
    CONSTRAINT value_attribute_fk FOREIGN KEY (attibute_id) REFERENCES "attributes" (id) ON DELETE CASCADE,
    CONSTRAINT value_entity_fk FOREIGN KEY (film_id) REFERENCES films (id) ON DELETE CASCADE
);
CREATE INDEX attribute_values_attibute_id_idx ON public.attribute_values USING btree (attibute_id);
CREATE INDEX attribute_values_film_id_idx ON public.attribute_values USING btree (film_id);
CREATE INDEX attribute_values_value_date_idx ON public.attribute_values USING btree (value_date);
