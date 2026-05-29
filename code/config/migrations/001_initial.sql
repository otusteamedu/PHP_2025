-- ======================================================================
-- Начальная миграция МКД Чат-бота
-- ======================================================================

-- 1. ENUM-типы

CREATE TYPE news_status AS ENUM ('pending', 'delivering', 'delivered');
CREATE TYPE news_delivery_status AS ENUM ('pending', 'sent', 'failed');
CREATE TYPE contact_type AS ENUM ('uk', 'council');
CREATE TYPE conversation_step AS ENUM ('main_menu', 'awaiting_subject', 'awaiting_description', 'preview', 'awaiting_question');
CREATE TYPE queue_name_type AS ENUM ('mkd.telegram.forward', 'mkd.rag.query', 'mkd.news.delivery');
CREATE TYPE messenger_type_enum AS ENUM ('max', 'telegram');
CREATE TYPE proposal_type AS ENUM ('feature', 'suggestion');

-- 2. Таблицы

-- Примечание: таблица schema_migrations создаётся MigrationRunner::ensureMigrationsTable()
-- до выполнения первой миграции — здесь не дублируем

-- Состояния многошагового диалога (одна активная сессия на пользователя)
CREATE TABLE conversation_states (
    user_id BIGINT PRIMARY KEY,
    current_step conversation_step NOT NULL DEFAULT 'main_menu'::conversation_step,
    data JSONB NOT NULL DEFAULT '{}',
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    expires_at TIMESTAMP NOT NULL DEFAULT (NOW() + INTERVAL '30 minutes')
);

-- Предложения (новый функционал и предложения совету)
CREATE TABLE proposals (
    id SERIAL PRIMARY KEY,
    type proposal_type NOT NULL DEFAULT 'feature'::proposal_type,
    user_id BIGINT NOT NULL,
    user_name VARCHAR(255) NOT NULL DEFAULT '',
    subject VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Контакты: управляющая компания и совет дома
CREATE TABLE contacts (
    id SERIAL PRIMARY KEY,
    type contact_type NOT NULL DEFAULT 'uk'::contact_type,
    name VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL DEFAULT '',
    phone VARCHAR(50) NOT NULL DEFAULT '',
    email VARCHAR(255) NOT NULL DEFAULT '',
    description TEXT NOT NULL DEFAULT '',
    sort INT NOT NULL DEFAULT 0
);

-- Сообщения из DLQ (Dead Letter Queue) для ручного разбора
CREATE TABLE fallback_messages (
    id SERIAL PRIMARY KEY,
    queue_name queue_name_type NOT NULL,
    message_body JSONB NOT NULL,
    error_message TEXT NOT NULL DEFAULT '',
    x_death_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Идемпотентность webhook
CREATE TABLE processed_webhooks (
    id SERIAL PRIMARY KEY,
    message_mid VARCHAR(255) NOT NULL,
    messenger_type messenger_type_enum NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

-- Новости
CREATE TABLE news (
    id SERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    priority INT DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    status news_status NOT NULL DEFAULT 'pending'::news_status
);

-- Доставки новостей
CREATE TABLE news_deliveries (
    news_id INT NOT NULL REFERENCES news(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL,
    status news_delivery_status NOT NULL DEFAULT 'pending'::news_delivery_status,
    delivered_at TIMESTAMP,
    PRIMARY KEY (news_id, user_id)
);

-- Подписчики бота
CREATE TABLE bot_subscribers (
    user_id BIGINT PRIMARY KEY,
    user_name TEXT,
    subscribed_at TIMESTAMP NOT NULL DEFAULT NOW(),
    is_active BOOLEAN NOT NULL DEFAULT TRUE
);

-- 3. Индексы

CREATE INDEX idx_conversation_states_expires_at ON conversation_states (expires_at);
CREATE INDEX idx_proposals_type ON proposals (type);
CREATE INDEX idx_proposals_user_id ON proposals (user_id);
CREATE INDEX idx_proposals_created_at ON proposals (created_at);
CREATE INDEX idx_contacts_type ON contacts (type);
CREATE INDEX idx_fallback_messages_queue_name ON fallback_messages (queue_name);
CREATE UNIQUE INDEX idx_processed_webhooks_mid_messenger ON processed_webhooks (message_mid, messenger_type);
CREATE INDEX idx_processed_webhooks_created_at ON processed_webhooks (created_at);
CREATE INDEX idx_news_deliveries_status ON news_deliveries(status);

-- 4. Функция очистки

CREATE OR REPLACE FUNCTION cleanup_processed_webhooks(
    retention_days INT DEFAULT 7
) RETURNS INT AS $$
DECLARE
    deleted_count INT;
BEGIN
    DELETE FROM processed_webhooks
    WHERE created_at < NOW() - (retention_days || ' days')::INTERVAL;

    GET DIAGNOSTICS deleted_count = ROW_COUNT;

    RETURN deleted_count;
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION cleanup_processed_webhooks IS 'Очистка processed_webhooks старше N дней (по умолчанию 7)';

-- 5. Комментарии

COMMENT ON TABLE conversation_states IS 'Состояния многошагового диалога с TTL 30 мин';
COMMENT ON COLUMN conversation_states.expires_at IS 'Время истечения сессии (TTL 30 мин)';
COMMENT ON TABLE proposals IS 'Предложения: новый функционал (feature) и предложения совету (suggestion)';
COMMENT ON COLUMN proposals.type IS 'Тип предложения: feature — новый функционал, suggestion — предложение совету';
COMMENT ON TABLE contacts IS 'Контакты: УК (uk) и совет дома (council)';
COMMENT ON COLUMN contacts.type IS 'Тип контакта: uk — управляющая компания, council — совет дома';
COMMENT ON COLUMN contacts.sort IS 'Порядок сортировки при отображении';
COMMENT ON TABLE fallback_messages IS 'Неудачные сообщения из DLQ для ручного разбора';
COMMENT ON TABLE processed_webhooks IS 'Идемпотентность webhook — предотвращение повторной обработки';
COMMENT ON COLUMN processed_webhooks.message_mid IS 'Идентификатор сообщения (строка, формат Max: mid.[0-9a-f]+)';

-- 6. Demo-данные

-- Контакты УК и совета дома
INSERT INTO contacts (type, name, role, phone, email, description, sort)
VALUES
    ('uk', 'ООО «ЖилСервис»', 'Управляющая компания', '+7 (123) 456-78-90', 'info@zhilservice.ru', 'Обслуживание многоквартирного дома: уборка, ремонт, содержание общего имущества', 1),
    ('council', 'Иванов Иван Иванович', 'Председатель совета дома', '+7 (234) 567-89-01', 'ivanov@mkd.example.com', 'Организация работы совета дома, взаимодействие с УК', 1),
    ('council', 'Петрова Мария Сергеевна', 'Заместитель председателя', '+7 (345) 678-90-12', 'petrova@mkd.example.com', 'Заместитель председателя совета дома, вопросы благоустройства', 2),
    ('council', 'Сидоров Алексей Петрович', 'Член совета дома', '+7 (456) 789-01-23', 'sidorov@mkd.example.com', 'Ответственный за вопросы безопасности и парковки', 3);

-- Начальная новость
INSERT INTO news (title, content, status)
VALUES ('Добро пожаловать!', 'Я — МКД-Бот, хранитель канала и ваш помощник. Можете писать мне в личные сообщения свои вопросы.', 'pending');
