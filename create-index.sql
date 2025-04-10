-- Indexes for table orders
CREATE INDEX IF NOT EXISTS "idx-orders-created_at" ON orders (created_at);                    -- Для ускорения фильтрации по дате оплаты заказа
CREATE INDEX IF NOT EXISTS "idx-orders-status" ON orders (status);                                  -- Для ускорения фильтрации по статусу заказа

-- Indexes for table session
CREATE INDEX IF NOT EXISTS "idx-sessions-film_id" ON session (film_id);                            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-room_id" ON session (room_id);                            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-sessions-start_time" ON session (start_time);                      -- Для ускорения сортировки по началу сеанса

-- Indexes for table tickets
CREATE INDEX IF NOT EXISTS "idx-tickets-order_id" ON tickets (order_id);                            -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-session_id" ON tickets (session_id);                        -- Для ускорения JOIN
CREATE INDEX IF NOT EXISTS "idx-tickets-row_number" ON tickets (row_number);
CREATE INDEX IF NOT EXISTS "idx-tickets-seat_number" ON tickets (seat_number);
CREATE INDEX IF NOT EXISTS "idx-tickets-price" ON tickets (price);                                  -- Для ускорения агрегации