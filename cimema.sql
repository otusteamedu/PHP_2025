create database cinema;

CREATE TABLE films (
                       id SERIAL PRIMARY KEY,
                       title VARCHAR(255),
                       release_year INT
);

INSERT INTO films (title, release_year)
VALUES ('A1', 2012),
       ('Красотка', 2015),
       ('Гонка', 2025),
       ('Динозавры', 2025);

CREATE TABLE attribute_types (
                                 id SERIAL PRIMARY KEY,
                                 name VARCHAR(100),
                                 value_type VARCHAR(20)
);

INSERT INTO attribute_types (name, value_type)
VALUES
    ('премия', 'boolean'),
    ('рецензия', 'text'),
    ('важная дата', 'date'),
    ('служебная дата', 'date');


CREATE TABLE attributes (
                            id SERIAL PRIMARY KEY,
                            name VARCHAR(100),
                            attribute_type_id INT REFERENCES attribute_types(id)
);

CREATE INDEX idx_attribute_type_id ON attributes(attribute_type_id);

INSERT INTO attributes (name, attribute_type_id)
VALUES
    ('Оскар', 1),
    ('Ника', 1),
    ('Рецензия кинокритика', 2),
    ('Премьера в РФ', 3),
    ('Дата начала продаж билетов', 4);

CREATE TABLE films_attribute_values (
                                        id SERIAL PRIMARY KEY,
                                        film_id INT REFERENCES films(id),
                                        attribute_id INT REFERENCES attributes(id),

                                        value_text TEXT,
                                        value_boolean BOOLEAN,
                                        value_date DATE
);

CREATE INDEX idx_values_date ON films_attribute_values(value_date);

INSERT INTO films_attribute_values (film_id, attribute_id, value_boolean)
VALUES (1, 1, true);

INSERT INTO films_attribute_values (film_id, attribute_id, value_boolean)
VALUES (2, 2, true);

INSERT INTO films_attribute_values (film_id, attribute_id, value_text)
VALUES (3, 3, 'Захватывающая драма, высоко оценена критиками.');

INSERT INTO films_attribute_values (film_id, attribute_id, value_date)
VALUES (4, 4, '2025-09-01');

INSERT INTO films_attribute_values (film_id, attribute_id, value_date)
VALUES (4, 5, '2025-07-5'),
       (1, 5,'2025-07-1'),
       (2, 5, '2025-07-1'),
       (3, 5, '2025-07-25');
INSERT INTO films_attribute_values (film_id, attribute_id, value_date)
VALUES (4, 5, '2025-07-28');

CREATE VIEW films_actualize AS
SELECT
    f.id,
    f.title,
    STRING_AGG(
            CASE
                WHEN fav.value_date <= CURRENT_DATE THEN f.title
                END, ', '
    ) AS actualize_now,
    STRING_AGG(
            CASE
                WHEN fav.value_date >= CURRENT_DATE + INTERVAL '20 days' THEN f.title
                END, ', '
    ) AS actualize_after_20_days

FROM films f
         LEFT JOIN films_attribute_values fav ON f.id = fav.film_id
         LEFT JOIN attributes a ON fav.attribute_id = a.id
         LEFT JOIN attribute_types b ON a.attribute_type_id = b.id
WHERE b.name = 'служебная дата'
GROUP BY f.id;

CREATE VIEW marketing AS
SELECT
    f.title AS фильм,
    t.name AS тип_атрибута,
    a.name AS атрибут,
    COALESCE(
            CAST(v.value_text AS TEXT),
            TO_CHAR(v.value_date, 'YYYY-MM-DD'),
            CAST(v.value_boolean AS TEXT)
    ) AS значение
FROM films f
         JOIN films_attribute_values v ON f.id = v.film_id
         JOIN attributes a ON v.attribute_id = a.id
         JOIN attribute_types t ON a.attribute_type_id = t.id
WHERE t.name IN ('премия', 'рецензия', 'важная дата','служебная дата');