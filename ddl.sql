-- table films
CREATE TABLE films (
    id       SERIAL PRIMARY KEY,
    title       VARCHAR(255) NOT NULL,
);


-- table attributes
CREATE TABLE attributes (
    id          SERIAL PRIMARY KEY,
    type_id     INT NOT NULL,
    name        VARCHAR(255) NOT NULL,
    CONSTRAINT "fk-attributes-type_id" FOREIGN KEY (type_id) REFERENCES attribute_types(id) ON DELETE CASCADE
);

-- index for the attributes table
CREATE INDEX "idx-attributes-type_id" ON attributes(type_id);


-- table attribute_types
CREATE TABLE attribute_types (
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    type        VARCHAR(255) NOT NULL,
);


-- table values
CREATE TABLE values (
    id              SERIAL PRIMARY KEY,
    film_id         INT NOT NULL,
    attribute_id    INT NOT NULL,
    text_value      TEXT NULL,
    boolean_value   BOOLEAN NULL,
    date_value      DATE NULL,
    string_value    VARCHAR(255) NULL,
    int_value       INT NULL,
    numeric_value   NUMERIC NULL,
    CONSTRAINT "fk-values-film_id" FOREIGN KEY (film_id) REFERENCES films(id) ON DELETE CASCADE,
    CONSTRAINT "fk-values-attribute_id" FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
);

-- index for the values table
CREATE INDEX "idx-values-film_id" ON values(film_id);
CREATE INDEX "idx-values-attribute_id" ON values(attribute_id);
