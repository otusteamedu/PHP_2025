-- Добавляем 'mkd.fallback' в ENUM queue_name_type
-- (для корректной записи DLQ-сообщений в fallback_messages)
ALTER TYPE queue_name_type ADD VALUE IF NOT EXISTS 'mkd.fallback';
