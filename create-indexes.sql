-- Indexes for table orders
CREATE INDEX IF NOT EXISTS "idx-orders-paid_at_date" ON orders (DATE(paid_at));                     -- Для ускорения фильтрации по дате оплаты заказа
CREATE INDEX IF NOT EXISTS "idx-orders-status" ON orders (status);                                  -- Для ускорения фильтрации по статусу заказа

-- Indexes for table sessions
CREATE INDEX IF NOT EXISTS "idx-sessions-movie_id" ON sessions (movie_id);                          -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-hall_id" ON sessions (hall_id);                            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-started_at" ON sessions (started_at);                      -- Для ускорения сортировки по дате и времени начала сеанса
CREATE INDEX IF NOT EXISTS "idx-sessions-started_at_date" ON sessions (DATE(started_at));           -- Для ускорения фильтрации по дате начала сеанса

-- Indexes for table seat_prices
CREATE INDEX IF NOT EXISTS "idx-seat_prices-session_id" ON seat_prices (session_id);                -- Для ускорения фильтрации по сеансу
CREATE INDEX IF NOT EXISTS "idx-seat_prices-seat_category_id" ON seat_prices (seat_category_id);    -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-seat_prices-price" ON seat_prices (price);                          -- Для ускорения агрегации

-- Indexes for table seats
CREATE INDEX IF NOT EXISTS "idx-seats-seat_category_id" ON seats (seat_category_id);                -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-seats-hall_id" ON seats (hall_id);                                  -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-seats-row" ON seats (row);                                          -- Для ускорения сортировки по рядам
CREATE INDEX IF NOT EXISTS "idx-seats-seat_number" ON seats (seat_number);                          -- Для ускорения сортировки по местам

-- Indexes for table tickets
CREATE INDEX IF NOT EXISTS "idx-tickets-order_id" ON tickets (order_id);                            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-session_id" ON tickets (session_id);                        -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-seat_id" ON tickets (seat_id);                              -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-price" ON tickets (price);                                  -- Для ускорения агрегации
