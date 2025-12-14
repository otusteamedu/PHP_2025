-- Удаление таблиц если существуют (обратный порядок из-за внешних ключей)
DROP TABLE IF EXISTS values;
DROP TABLE IF EXISTS attributes;
DROP TABLE IF EXISTS entities;
DROP TABLE IF EXISTS types_attributes;

-- Таблица сущностей (фильмов)
CREATE TABLE entities (
    entity_id SERIAL PRIMARY KEY,
    title TEXT NOT NULL
);

COMMENT ON TABLE entities IS 'Таблица сущностей';
COMMENT ON COLUMN entities.title IS 'Название экземпляра сущности';


-- Таблица типов атрибутов
CREATE TABLE types_attributes (
    type_id SERIAL PRIMARY KEY,
    name VARCHAR(64) NOT NULL UNIQUE
);

COMMENT ON TABLE types_attributes IS 'Таблица типов атрибутов';
COMMENT ON COLUMN types_attributes.name IS 'Название типа';

-- Таблица атрибутов
CREATE TABLE attributes (
    attribute_id SERIAL PRIMARY KEY,
    name VARCHAR(256) NOT NULL UNIQUE,
    type_id INTEGER NOT NULL REFERENCES types_attributes(type_id)
);

COMMENT ON TABLE attributes IS 'Таблица атрибутов';
COMMENT ON COLUMN attributes.name IS 'Название атрибута';

-- Таблица значений атрибутов
CREATE TABLE values (
    entity_id INTEGER NOT NULL REFERENCES entites(entity_id),
    attribute_id INTEGER NOT NULL REFERENCES attributes(attribute_id),
    value_int INTEGER,
    value_float DECIMAL,
    value_string TEXT,
    value_bool BOOLEAN,
    value_datetime TIMESTAMP,
    PRIMARY KEY (entity_id, attribute_id)
);

COMMENT ON TABLE values IS 'Таблица значений атрибутов';
COMMENT ON COLUMN values.value_int IS 'Значение атрибута типа "целое число"';
COMMENT ON COLUMN values.value_float IS 'Значение атрибута типа "число с плавающей точкой"';
COMMENT ON COLUMN values.value_string IS 'Значение атрибута типа "строка"';
COMMENT ON COLUMN values.value_bool IS 'Значение атрибута типа "логический"';
COMMENT ON COLUMN values.value_datetime IS 'Значение атрибута типа "дата\время"';