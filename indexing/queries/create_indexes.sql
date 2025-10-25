-- индексы для запросов 1 и 3
CREATE INDEX IF NOT EXISTS idx_screening_show_date ON screening (show_date);

-- индексы для запросов 2 и 4
CREATE INDEX IF NOT EXISTS idx_ticket_purchase_time ON ticket (purchase_time);

-- составной индекс для запроса 3
CREATE INDEX IF NOT EXISTS idx_screening_show_date_time ON screening (show_date, show_time);

-- индексы для JOIN операций в запросе 4
CREATE INDEX IF NOT EXISTS idx_ticket_screening_id ON ticket (screening_id);
CREATE INDEX IF NOT EXISTS idx_screening_movie_id ON screening (movie_id);

-- составной индекс для запроса 4
CREATE INDEX IF NOT EXISTS idx_ticket_purchase_screening_price ON ticket (purchase_time, screening_id, price);

-- индекс для запроса 5
CREATE INDEX IF NOT EXISTS idx_screening_hall_id ON screening (hall_id);

ANALYZE;
