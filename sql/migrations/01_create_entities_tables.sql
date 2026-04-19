DROP VIEW IF EXISTS vw_marketing_export;
DROP VIEW IF EXISTS vw_service_tasks;

DROP TABLE IF EXISTS attribute_values CASCADE;
DROP TABLE IF EXISTS attributes CASCADE;
DROP TABLE IF EXISTS attribute_types CASCADE;
DROP TABLE IF EXISTS movies CASCADE;

CREATE TABLE movies
(
    id         BIGSERIAL PRIMARY KEY,
    title      VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT NOW()
);

CREATE TABLE attribute_types
(
    id   BIGSERIAL PRIMARY KEY,
    code VARCHAR(50)  NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE attributes
(
    id                BIGSERIAL PRIMARY KEY,
    attribute_type_id BIGINT       NOT NULL REFERENCES attribute_types (id) ON DELETE RESTRICT,
    code              VARCHAR(100) NOT NULL UNIQUE,
    name              VARCHAR(255) NOT NULL,
    data_type         VARCHAR(20)  NOT NULL CHECK (data_type IN ('text', 'boolean', 'date', 'numeric')),
    description       TEXT
);

CREATE TABLE attribute_values
(
    id            BIGSERIAL PRIMARY KEY,
    movie_id      BIGINT    NOT NULL REFERENCES movies (id) ON DELETE CASCADE,
    attribute_id  BIGINT    NOT NULL REFERENCES attributes (id) ON DELETE CASCADE,

    value_text    TEXT,
    value_boolean BOOLEAN,
    value_date    DATE,
    value_numeric NUMERIC,

    created_at    TIMESTAMP NOT NULL DEFAULT NOW(),

    CONSTRAINT uq_attribute_values_movie_attribute UNIQUE (movie_id, attribute_id),

    CONSTRAINT chk_one_value_only CHECK (
        (
            CASE WHEN value_text IS NOT NULL THEN 1 ELSE 0 END +
            CASE WHEN value_boolean IS NOT NULL THEN 1 ELSE 0 END +
            CASE WHEN value_date IS NOT NULL THEN 1 ELSE 0 END +
            CASE WHEN value_numeric IS NOT NULL THEN 1 ELSE 0 END
            ) = 1
        )
);
