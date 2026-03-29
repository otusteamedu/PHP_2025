CREATE TABLE
    film (id SERIAL PRIMARY KEY, name VARCHAR(255) NOT NULL);

CREATE TABLE
    atr_type (
        id SERIAL PRIMARY KEY,
        type_name VARCHAR(255) NOT NULL,
        base_type VARCHAR(255) NOT NULL
    );

CREATE TABLE
    atr_film (
        id SERIAL PRIMARY KEY,
        id_atr_type INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        FOREIGN KEY (id_atr_type) REFERENCES atr_type (id)
    );

CREATE TABLE
    atr_value (
        id SERIAL PRIMARY KEY,
        id_film INT NOT NULL,
        id_atr_film INT NOT NULL,
        int_value INT,
        text_value TEXT,
        decimal_value DECIMAL(10,2),
        date_value DATE,
        boolean_value BOOLEAN,
        FOREIGN KEY (id_film) REFERENCES film (id),
        FOREIGN KEY (id_atr_film) REFERENCES atr_film (id)
    );