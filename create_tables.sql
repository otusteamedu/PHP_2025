CREATE TABLE movie
(
    id    SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE attribute
(
    id    SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE attribute_type
(
    id           SERIAL PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    type         VARCHAR(255) NOT NULL,
    attribute_id INT UNIQUE,
    CONSTRAINT fk_attribute
        FOREIGN KEY (attribute_id)
            REFERENCES attribute (id)
            ON DELETE CASCADE
);

CREATE TABLE movie_attribute_value
(
    id              SERIAL PRIMARY KEY,
    movie_id        INT            NOT NULL,
    attribute_id    INT            NOT NULL,
    string_value    VARCHAR(255)   DEFAULT NULL,
    text_value      TEXT           DEFAULT NULL,
    integer_value   INTEGER        DEFAULT NULL,
    float_value     REAL           DEFAULT NULL,
    money_value     NUMERIC(15, 2) DEFAULT NULL,
    boolean_value   BOOLEAN        DEFAULT NULL,
    date_value      DATE           DEFAULT NULL,
    timestamp_value TIMESTAMP      DEFAULT NULL,
    CONSTRAINT fk_movie
        FOREIGN KEY (movie_id)
            REFERENCES movie (id)
            ON DELETE CASCADE,
    CONSTRAINT fk_attribute
        FOREIGN KEY (attribute_id)
            REFERENCES attribute (id)
            ON DELETE CASCADE
);

CREATE INDEX idx_movie_title ON movie(title);
CREATE INDEX idx_attribute_title ON attribute(title);

INSERT INTO movie (id, title)
VALUES (1, 'Властелин колец'),
       (2, 'Звездные войны'),
       (3, 'Голяк');

INSERT INTO attribute (id, title)
VALUES (1, 'Жанр'),
       (2, 'Рецензия'),
       (3, 'Рейтинг'),
       (4, 'Мировые сборы'),
       (5, 'Стоимость'),
       (6, 'Премия'),
       (7, 'Дата выпуска'),
       (8, 'Конец показа');

INSERT INTO attribute_type (attribute_id, title, type)
VALUES (1, 'Строка', 'string'),
       (2, 'Текст', 'text'),
       (3, 'Число', 'integer'),
       (4, 'Число с плавающей точкой', 'float'),
       (5, 'Деньги', 'money'),
       (6, 'Логическое значение', 'boolean'),
       (7, 'Дата', 'date'),
       (8, 'Дата и время', 'datetime');

INSERT INTO movie_attribute_value (movie_id, attribute_id, string_value, text_value, integer_value, float_value,
                                   money_value, boolean_value, date_value, timestamp_value)
VALUES (1, 1, 'Фэнтези', null, null, null, null, null, null, null),
       (1, 2, null, 'Большая и длинная рецензия', null, null, null, null, null, null),
       (1, 3, null, null, 8, null, null, null, null, null),
       (1, 4, null, null, null, '100000.00', null, null, null, null),
       (1, 5, null, null, null, null, '10.00', null, null, null),
       (1, 6, null, null, null, null, null, false, null, null),
       (1, 7, null, null, null, null, null, null, '2002-02-07', null),
       (1, 8, null, null, null, null, null, null, null, '2025-03-26 12:00'),
       (2, 1, 'Фантастика', null, null, null, null, null, null, null),
       (2, 2, null, 'Большая и длинная рецензия', null, null, null, null, null, null),
       (2, 3, null, null, 8, null, null, null, null, null),
       (2, 4, null, null, null, '100000.00', null, null, null, null),
       (2, 5, null, null, null, null, '10.00', null, null, null),
       (2, 6, null, null, null, null, null, false, null, null),
       (2, 7, null, null, null, null, null, null, '1977-05-25', null),
       (2, 8, null, null, null, null, null, null, null, '2025-03-07 12:00'),
       (3, 8, null, null, null, null, null, null, null, '2025-03-08 12:00');