CREATE TABLE MOVIE (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration_minutes INT NOT NULL,
    genre VARCHAR(100),
    rating DECIMAL(3,1)
);
