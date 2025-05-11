DROP TABLE IF EXISTS offers;
DROP TABLE IF EXISTS category;


CREATE TABLE IF NOT EXISTS category
(
    id      INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name    VARCHAR(255) NOT NULL UNIQUE,
    code    VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS offers
(
    id              INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    category_id     INT NOT NULL,
    name            VARCHAR(255) NOT NULL,
    color           VARCHAR(255) NOT NULL,
    price           DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES category (id)
);
