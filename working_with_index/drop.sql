-- Индексы для табличны заказов
DROP INDEX IF EXISTS "idx-orders-paid_at_date";
DROP INDEX IF EXISTS "idx-orders-status";

-- Индексы для табличны сессий
DROP INDEX IF EXISTS "idx-sessions-film_id";
DROP INDEX IF EXISTS "idx-sessions-hall_id";
DROP INDEX IF EXISTS "idx-sessions-created_at";
DROP INDEX IF EXISTS "idx-sessions-created_at_date";

-- Индексы для табличны прайс-лист
DROP INDEX IF EXISTS "idx-price_list-session_id";
DROP INDEX IF EXISTS "idx-price_list-place_category_id";
DROP INDEX IF EXISTS "idx-price_list-price";

-- Индексы для табличны мест
DROP INDEX IF EXISTS "idx-places-place_category_id";
DROP INDEX IF EXISTS "idx-places-hall_id";
DROP INDEX IF EXISTS "idx-places-row";
DROP INDEX IF EXISTS "idx-places-number";

-- Индексы для табличных билетов
DROP INDEX IF EXISTS "idx-tickets-order_id";
DROP INDEX IF EXISTS "idx-tickets-session_id";
DROP INDEX IF EXISTS "idx-tickets-place_id";
DROP INDEX IF EXISTS "idx-tickets-price";
