-- Добавление колонки unsubscribed_at и индекса на is_active для bot_subscribers
ALTER TABLE bot_subscribers ADD COLUMN IF NOT EXISTS unsubscribed_at TIMESTAMP DEFAULT NULL;
COMMENT ON COLUMN bot_subscribers.unsubscribed_at IS 'Время отписки пользователя (bot_stopped)';
CREATE INDEX IF NOT EXISTS idx_bot_subscribers_is_active ON bot_subscribers(is_active);
