-- индексы для запросов 1 и 3
CREATE INDEX IF NOT EXISTS idx_screenings_date_start ON "cinema".screenings (DATE("SCREENING_START"));

-- индексы для запросов 2, 4, 5, 6
CREATE INDEX idx_tickets_screening_id on "cinema".tickets("SCREENING_ID");

-- индексы для запросов 2 и 4
CREATE INDEX IF NOT EXISTS idx_tickets_purchase_time ON "cinema".tickets (DATE("PURCHASE_DATE"));

-- индексы для JOIN операций в запросе 1, 3, 4
CREATE INDEX IF NOT EXISTS idx_screening_movie_id ON "cinema".screenings ("MOVIE_ID");

-- индекс для запроса 5, 6
CREATE INDEX IF NOT EXISTS idx_screenings_hall_id ON "cinema".screenings ("HALL_ID");
