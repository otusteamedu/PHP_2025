CREATE TABLE products
(
    id         BIGSERIAL PRIMARY KEY,
    brand      VARCHAR(255) NOT NULL,
    title      VARCHAR(255) NOT NULL,
    price      INT          NOT NULL,
    capacity   INT          NOT NULL,
    hidden     BOOLEAN      NOT NULL,
    created_at TIMESTAMPTZ  NOT NULL,
    updated_at TIMESTAMPTZ  NOT NULL
);
