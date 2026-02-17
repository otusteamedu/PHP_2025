create database cinema;

CREATE TABLE films
(
    id           SERIAL PRIMARY KEY,
    title        VARCHAR(255),
    release_year INT
);

CREATE INDEX idx_film_id ON films (id);
CREATE INDEX idx_films_release_year ON films(release_year);

INSERT INTO films (title, release_year)
SELECT random_string(12),
       random() * 5 + 2020
FROM generate_series(1, 10000000);

CREATE TABLE attribute_types
(
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(100),
    value_type VARCHAR(20)
);

INSERT INTO attribute_types (name, value_type)
VALUES ('премия', 'boolean'),
       ('рецензия', 'text'),
       ('важная дата', 'date'),
       ('служебная дата', 'date');


CREATE TABLE attributes
(
    id                SERIAL PRIMARY KEY,
    name              VARCHAR(100),
    attribute_type_id INT REFERENCES attribute_types (id)
);

CREATE INDEX idx_attribute_type_id ON attributes (attribute_type_id);

INSERT INTO attributes (name, attribute_type_id)
VALUES ('Оскар', 1),
       ('Ника', 1),
       ('Рецензия кинокритика', 2),
       ('Премьера в РФ', 3),
       ('Дата начала продаж билетов', 4),
       ('Дата конца продаж билетов', 4);

CREATE TABLE films_attribute_values
(
    id            SERIAL PRIMARY KEY,
    film_id       INT REFERENCES films (id),
    attribute_id  INT REFERENCES attributes (id),

    value_int     integer,
    value_float   DOUBLE PRECISION,
    value_text    TEXT,
    value_boolean BOOLEAN,
    value_date    DATE
);

CREATE INDEX idx_values_date ON films_attribute_values (value_date);

INSERT INTO films_attribute_values (film_id, attribute_id, value_boolean)
VALUES (1, 1, true);

INSERT INTO films_attribute_values (film_id, attribute_id, value_boolean)
VALUES (2, 2, true);

INSERT INTO films_attribute_values (film_id, attribute_id, value_text)
VALUES (3, 3, 'Захватывающая драма, высоко оценена критиками.');

INSERT INTO films_attribute_values (film_id, attribute_id, value_date)
VALUES (4, 4, '2025-09-01');

INSERT INTO films_attribute_values (film_id, attribute_id, value_date)
SELECT trunc(random() * 10000000 + 1)::int,
        5,
       '2020-12-31'::date + trunc(random() * 1096)::int
FROM generate_series(1, 10000000);

INSERT INTO films_attribute_values (film_id, attribute_id, value_date)
SELECT trunc(random() * 10000000 + 1)::int,
        6,
       '2023-12-31'::date + trunc(random() * 1096)::int
FROM generate_series(1, 10000000);

INSERT INTO films_attribute_values (film_id, attribute_id, value_date)
VALUES (4, 5, '2025-07-28');

CREATE table session
(
    id         SERIAL PRIMARY KEY,
    film_id    integer,
    started_at timestamp,
    FOREIGN KEY (film_id) REFERENCES films (id)
);
INSERT INTO session (film_id, started_at)
SELECT trunc(random() * 10000000 + 1)::int,
        '2020-12-31'::date + trunc(random() * 2096)::int
FROM generate_series(1, 10000000);

CREATE TABLE session_seat_price
(
    id         SERIAL PRIMARY KEY,
    session_id integer,
    place_id   integer,
    price      DECIMAL(10, 2),
    FOREIGN KEY (place_id) REFERENCES hall (id),
    FOREIGN KEY (session_id) REFERENCES session (id)
);

INSERT INTO session_seat_price (session_id, place_id, price)
SELECT session_id, place_id, random()
FROM (SELECT s.id AS session_id,
             p.id AS place_id
      FROM generate_series(1, 100000) AS s(id),
           generate_series(1, 1000) AS p(id)) AS all_combinations
ORDER BY random()
    LIMIT 10000000;

CREATE INDEX idx_price ON session_seat_price(price);

CREATE table orders
(
    id         SERIAL PRIMARY KEY,
    film_id    integer,
    session_id integer,
    place_id   integer,
    cost       DECIMAL(10, 2),
    created_at DATE,
    FOREIGN KEY (place_id) REFERENCES hall (id),
    FOREIGN KEY (session_id) REFERENCES session (id)
);

CREATE INDEX idx_orders_created_at ON orders (created_at);
CREATE INDEX idx_orders_film_date ON orders(film_id, created_at);
CREATE INDEX idx_session_id ON orders (session_id, place_id);


INSERT INTO orders(film_id, session_id, place_id, cost, created_at)
SELECT trunc(random() * 10000 + 1)::int,
        trunc(random() * 10000 + 1)::int,
        trunc(random() * 10000 + 1)::int,
        random(),
       '2025-08-10'::date + (random() * 30)::int
FROM generate_series(1, 10000000);

CREATE table hall
(
    id    SERIAL PRIMARY KEY,
    place INT,
    line  INT,
    price DECIMAL(10, 2)
);

INSERT INTO hall (place, line, price)
SELECT trunc(random() * 10000 + 1)::int, trunc(random() * 10000 + 1)::int, trunc(random() * 100 + 6)::int
FROM generate_series(1, 10000000);

-- 1. Выбор всех фильмов на сегодня
EXPLAIN ANALYZE
SELECT *
FROM films WHERE release_year = 2025;

-- 2. Подсчёт проданных билетов за неделю
EXPLAIN ANALYZE
SELECT count(orders.id)
FROM orders
WHERE created_at BETWEEN '2025-08-04' AND '2025-08-11';

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
EXPLAIN ANALYZE
SELECT DISTINCT f.id
FROM films f
         JOIN films_attribute_values v_start ON v_start.film_id = f.id AND v_start.attribute_id = 5
         JOIN films_attribute_values v_end ON v_end.film_id = f.id AND v_end.attribute_id = 6
WHERE v_start.value_date <= '2025-08-11'
  AND v_end.value_date >= '2025-08-11';

-- 4. Поиск 3 самых прибыльных фильмов за неделю
SET work_mem = '128MB';

EXPLAIN ANALYZE
select films.id, SUM(orders.cost) AS total_profit
from films
         join orders on films.id = orders.film_id
WHERE orders.created_at BETWEEN '2025-07-11' AND '2025-08-11'
GROUP BY films.id
order by total_profit DESC
    limit 3;

-- 5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
EXPLAIN ANALYZE
SELECT h.id,
       h.place,
       h.line,
       CASE
           WHEN o.id IS NOT NULL THEN 'занято'
           ELSE 'свободно'
           END AS status
FROM hall h
         LEFT JOIN orders o ON h.id = o.place_id AND o.session_id = 1
ORDER BY h.place, h.line;

-- 6. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс
EXPLAIN ANALYZE
SELECT (SELECT price FROM session_seat_price WHERE session_id = 1 ORDER BY price DESC LIMIT 1) AS max_price,
       (SELECT price FROM session_seat_price WHERE session_id = 1 ORDER BY price LIMIT 1)      AS min_price;

SELECT count(id) from session_seat_price;

-- результаты запросов в файле result.sql

-- отсортированный список (15 значений) самых больших по размеру объектов БД (таблицы, включая индексы, сами индексы)
SELECT
    pg_size_pretty(pg_total_relation_size(c.oid)) AS total_size,
    pg_size_pretty(pg_relation_size(c.oid)) AS table_size,
    pg_size_pretty(pg_total_relation_size(c.oid) - pg_relation_size(c.oid)) AS index_size,
    c.relname AS object_name,
    CASE c.relkind
        WHEN 'r' THEN 'table'
        WHEN 'i' THEN 'index'
        WHEN 'm' THEN 'materialized view'
        WHEN 't' THEN 'TOAST table'
        ELSE c.relkind
        END AS object_type
FROM pg_class c
         JOIN pg_namespace n ON n.oid = c.relnamespace
WHERE n.nspname NOT IN ('pg_catalog', 'information_schema')
  AND c.relkind IN ('r', 'i', 'm', 't')
ORDER BY pg_total_relation_size(c.oid) DESC
    LIMIT 15;

-- отсортированные списки (по 5 значений) самых часто и редко используемых индексов
SELECT
    i.relname AS index_name,
    t.relname AS table_name,
    s.idx_scan AS index_scans,
    pg_size_pretty(pg_relation_size(i.oid)) AS index_size
FROM pg_stat_user_indexes s
         JOIN pg_index ix ON ix.indexrelid = s.indexrelid
         JOIN pg_class i ON i.oid = s.indexrelid
         JOIN pg_class t ON t.oid = s.relid
WHERE s.idx_scan IS NOT NULL
ORDER BY s.idx_scan DESC
    LIMIT 5;

SELECT
    i.relname AS index_name,
    t.relname AS table_name,
    s.idx_scan AS index_scans,
    pg_size_pretty(pg_relation_size(i.oid)) AS index_size
FROM pg_stat_user_indexes s
         JOIN pg_index ix ON ix.indexrelid = s.indexrelid
         JOIN pg_class i ON i.oid = s.indexrelid
         JOIN pg_class t ON t.oid = s.relid
WHERE s.idx_scan IS NOT NULL
ORDER BY s.idx_scan
    LIMIT 5;



Create or replace function random_string(length integer) returns text as
$$
declare
chars  text[]  := '{0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z}';
    result text    := '';
    i      integer := 0;
begin
    if length < 0 then
        raise exception 'Given length cannot be less than 0';
end if;
for i in 1..length
        loop
            result := result || chars[1 + random() * (array_length(chars, 1) - 1)];
end loop;
return result;
end;
$$ language plpgsql;