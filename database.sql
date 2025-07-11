-- Создание таблицы users
CREATE TABLE users
(
    id         SERIAL PRIMARY KEY,
    name       VARCHAR(100)        NOT NULL,
    email      VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    is_active  BOOLEAN                  DEFAULT TRUE,
    age        INTEGER CHECK (age >= 0)
);

-- Заполнение таблицы 100 тестовыми записями
INSERT INTO users (name, email, created_at, is_active, age)
SELECT 'User ' || i,
       'user' || i || '@example.com',
       CURRENT_TIMESTAMP - (i * INTERVAL '1 day'),
       CASE WHEN i % 10 <> 0 THEN TRUE ELSE FALSE END,
       (random() * 70 + 10)::integer
FROM generate_series(1, 100) AS i;

-- Создание индексов для улучшения производительности
CREATE INDEX idx_users_email ON users (email);
CREATE INDEX idx_users_created_at ON users (created_at);
CREATE INDEX idx_users_is_active ON users (is_active) WHERE is_active = TRUE;