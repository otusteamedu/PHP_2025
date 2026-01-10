CREATE TABLE cinema (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE rooms (
    id SERIAL PRIMARY KEY,
    cinema_id INT REFERENCES cinema(id) ON DELETE CASCADE,
    name VARCHAR(50) NOT NULL,
    layout JSONB	
);

CREATE TABLE films (
    id SERIAL PRIMARY KEY,
    title VARCHAR(200) NOT NULL
);

CREATE TABLE session (
    id SERIAL PRIMARY KEY,
    room_id INT REFERENCES rooms(id) ON DELETE CASCADE,
    film_id INT REFERENCES films(id) ON DELETE CASCADE,
    start_time TIMESTAMP NOT NULL,
    base_price DECIMAL(10,2) NOT NULL
);

CREATE TABLE clients (
    id SERIAL PRIMARY KEY,
    fio VARCHAR(100) NOT NULL,
    phone VARCHAR(100) UNIQUE NOT NULL
);

CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    client_id INT REFERENCES clients(id) ON DELETE CASCADE,
    status VARCHAR(20) NOT NULL CHECK (status IN ('pending', 'paid', 'cancelled')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tickets (
    id SERIAL PRIMARY KEY,
    session_id INT REFERENCES session(id) ON DELETE CASCADE,
    order_id INT REFERENCES orders(id) ON DELETE CASCADE,
    seat_number VARCHAR(10) NOT NULL,
    row_number VARCHAR(10) NOT NULL,
    price DECIMAL(10,2) DEFAULT NULL
);

CREATE TABLE payment (
    id SERIAL PRIMARY KEY,
    order_id INT REFERENCES orders(id) ON DELETE CASCADE,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL CHECK (payment_method IN ('card', 'cash', 'bonus', 'online')),
    status VARCHAR(20) NOT NULL CHECK (status IN ('success', 'failed', 'refund')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT m.title, SUM(p.amount) AS total_revenue
FROM payment p
         JOIN orders o ON p.order_id = o.id
         JOIN tickets t ON o.id = t.order_id
         JOIN session s ON t.session_id = s.id
         JOIN films m ON s.film_id = m.id
WHERE p.status = 'success'
GROUP BY m.title
ORDER BY total_revenue DESC
    LIMIT 1;