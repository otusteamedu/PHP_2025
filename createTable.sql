CREATE TABLE films (
    film_id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    release_date DATE NOT NULL
);
CREATE TABLE attribute_types (
    type_id SERIAL PRIMARY KEY,
    type_name VARCHAR(50) NOT NULL
); 
CREATE TABLE attributes (
    attribute_id SERIAL PRIMARY KEY,
    attribute_name VARCHAR(255) NOT NULL,
    type_id INT NOT NULL,
    FOREIGN KEY (type_id) REFERENCES attribute_types(type_id)
);
CREATE TABLE values (
    value_id SERIAL PRIMARY KEY,
    film_id INT NOT NULL,
    attribute_id INT NOT NULL,
    value_text TEXT,
    value_date DATE,
    value_boolean BOOLEAN,
    value_float FLOAT,
    value_int INT,
    FOREIGN KEY (film_id) REFERENCES films(film_id),
    FOREIGN KEY (attribute_id) REFERENCES attributes(attribute_id)
);
