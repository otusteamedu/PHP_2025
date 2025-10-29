-- Очистка старой схемы
DROP TABLE IF EXISTS attribute_values CASCADE;
DROP TABLE IF EXISTS attributes CASCADE;
DROP TABLE IF EXISTS attribute_types CASCADE;
DROP TABLE IF EXISTS titles CASCADE;


-- Таблицы
CREATE TABLE titles
(
    id    BIGSERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL
);

CREATE TABLE attribute_types
(
    id   BIGSERIAL PRIMARY KEY,
    name VARCHAR(64) NOT NULL UNIQUE
);

CREATE TABLE attributes
(
    id      BIGSERIAL PRIMARY KEY,
    type_id BIGINT       NOT NULL REFERENCES attribute_types (id) ON DELETE CASCADE,
    name    VARCHAR(128) NOT NULL,
    UNIQUE (type_id, name)
);

CREATE TABLE attribute_values
(
    id            BIGSERIAL PRIMARY KEY,
    title_id      BIGINT NOT NULL REFERENCES titles (id) ON DELETE CASCADE,
    attribute_id  BIGINT NOT NULL REFERENCES attributes (id) ON DELETE CASCADE,
    value_text    TEXT NULL,
    value_boolean BOOLEAN NULL,
    value_date    DATE NULL,
    CHECK ( (value_text IS NOT NULL):: int + (value_boolean IS NOT NULL):: int + (value_date IS NOT NULL):: int = 1
) );

-- Индексы

CREATE INDEX idx_attributes_type_id ON attributes (type_id);

CREATE INDEX idx_attribute_values_title_id ON attribute_values (title_id);

CREATE INDEX idx_attribute_values_attribute_id ON attribute_values (attribute_id);

CREATE INDEX idx_attribute_values_type_attr ON attribute_values (title_id, attribute_id);

CREATE INDEX idx_attribute_values_value_text_trgm ON attribute_values USING gin (value_text gin_trgm_ops);

CREATE INDEX idx_attribute_values_value_boolean ON attribute_values (value_boolean);

CREATE INDEX idx_attribute_values_value_date ON attribute_values (value_date);

CREATE INDEX idx_attribute_values_composite on attribute_values (attribute_id, value_date, value_boolean);

