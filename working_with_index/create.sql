-- Индексы для табличны заказов
CREATE INDEX IF NOT EXISTS "idx-orders-paid_at_date" ON orders (DATE(paid_at));                     -- Для ускорения фильтрации по дате оплаты заказа
CREATE INDEX IF NOT EXISTS "idx-orders-status" ON orders (status);                                  -- Для ускорения фильтрации по статусу заказа

-- Индексы для табличны сессий
CREATE INDEX IF NOT EXISTS "idx-sessions-film_id" ON sessions (film_id);                            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-hall_id" ON sessions (hall_id);                            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-created_at" ON sessions (created_at);                      -- Для ускорения сортировки по дате и времени начала сеанса
CREATE INDEX IF NOT EXISTS "idx-sessions-created_at_date" ON sessions (DATE(created_at));           -- Для ускорения фильтрации по дате начала сеанса

-- Индексы для табличны прайс-лист
CREATE INDEX IF NOT EXISTS "idx-price_list-session_id" ON price_list (session_id);                  -- Для ускорения фильтрации по сеансу
CREATE INDEX IF NOT EXISTS "idx-price_list-place_category_id" ON price_list (place_category_id);    -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-price_list-price" ON price_list (price);                            -- Для ускорения агрегации

-- Индексы для табличны мест
CREATE INDEX IF NOT EXISTS "idx-places-place_category_id" ON places (place_category_id);            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-places-hall_id" ON places (hall_id);                                -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-places-row" ON places (row);                                        -- Для ускорения сортировки по рядам
CREATE INDEX IF NOT EXISTS "idx-places-number" ON places (number);                                  -- Для ускорения сортировки по местам

-- Индексы для табличных билетов
CREATE INDEX IF NOT EXISTS "idx-tickets-order_id" ON tickets (order_id);                            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-session_id" ON tickets (session_id);                        -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-place_id" ON tickets (place_id);                            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-price" ON tickets (price);                                  -- Для ускорения агрегации
