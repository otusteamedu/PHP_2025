-- Создание таблицы заявок на банковские выписки
CREATE TABLE IF NOT EXISTS bank_statement_orders (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    status VARCHAR(32) NOT NULL DEFAULT 'queued',
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NULL
);

-- Логи отправки уведомлений
CREATE TABLE IF NOT EXISTS bank_statement_order_logs (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES bank_statement_orders(id) ON DELETE CASCADE,
    email VARCHAR(255) NOT NULL,
    success BOOLEAN NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);
