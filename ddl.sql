CREATE TABLE movies
(
    id   SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE attribute_types
(
    id        SERIAL PRIMARY KEY,
    name      VARCHAR(255) NOT NULL,
    data_type VARCHAR(100) NOT NULL
);

CREATE TABLE attributes
(
    id      SERIAL PRIMARY KEY,
    type_id INT          NOT NULL,
    name    VARCHAR(255) NOT NULL,
    CONSTRAINT "fk-attributes-type_id" FOREIGN KEY (type_id) REFERENCES attribute_types(id) ON DELETE CASCADE
);

CREATE TABLE attribute_values
(
    id            SERIAL PRIMARY KEY,
    movie_id      INT NOT NULL,
    attribute_id  INT NOT NULL,
    int_value     INT NULL,
    numeric_value NUMERIC NULL,
    boolean_value BOOLEAN NULL,
    date_value    DATE NULL,
    text_value    TEXT NULL,
    CONSTRAINT "fk-attribute_values-movie_id" FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    CONSTRAINT "fk-attribute_values-attribute_id" FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
);

CREATE INDEX "idx-attributes-type_id" ON attributes(type_id);
CREATE INDEX "idx-attribute_values-movie_id" ON attribute_values(movie_id);
CREATE INDEX "idx-attribute_values-attribute_id" ON attribute_values(attribute_id);
