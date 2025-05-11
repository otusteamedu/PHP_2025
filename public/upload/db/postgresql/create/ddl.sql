DROP TABLE IF EXISTS offers;
DROP TABLE IF EXISTS category;

CREATE TABLE IF NOT EXISTS category
(
    id      SERIAL PRIMARY KEY,
    name    VARCHAR(255) NOT NULL UNIQUE,
    code    VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS offers
(
    id              SERIAL PRIMARY KEY,
    category_id     INT NOT NULL,
    name            VARCHAR(255) NOT NULL,
    color           VARCHAR(255) NOT NULL,
    price           DECIMAL(10, 2) NOT NULL,
    CONSTRAINT "fk-offers-category_id" FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL
);

CREATE INDEX IF NOT EXISTS "idx-offers-category_id" ON offers (category_id);
