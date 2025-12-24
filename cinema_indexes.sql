-- Индекс для запросов 1, 2, 3, 4 (Фильтрации по диапазонам времени, важен для всех запросов с WHERE по времени)
CREATE INDEX idx_showtime_time ON showtime (time);

-- Индекс для запросов 1, 3, 4 (JOIN между showtime и movie, поиск названий фильмов по showtime и группировка выручки по фильмам)
CREATE INDEX idx_showtime_movie_id ON showtime (movie_id);

-- Индекс для запросов 3, 5, 6 (JOIN между showtime и hall, вывод названия зала в афише и получение информации о зале для конкретного сеанса)
CREATE INDEX idx_showtime_hall_id ON showtime (hall_id);

-- Индекс для запросов 2, 4, 5 (JOIN между order и showtime, подсчет билетов и выручки по сеансам и определение занятых мест (LEFT JOIN))
CREATE INDEX idx_order_showtime_id ON "order" (showtime_id);

-- Индекс для запроса 5 (LEFT JOIN для определения занятых мест, для быстрого поиска заказов по конкретному месту)
CREATE INDEX idx_order_seat_id ON "order" (seat_id);

-- Индекс для запросов 5, 6 (JOIN между seat и hall, для получения всех мест конкретного зала)
CREATE INDEX idx_seat_hall_id ON seat (hall_id);

-- Индекс для запросов 5, 6 (JOIN между seat и seat_type, для получения типа и цены места)
CREATE INDEX idx_seat_seat_type_id ON seat (seat_type_id);

-- Индекс для запросов 1, 3, 4 (Покрывающий индекс для запросов с WHERE по времени и JOIN по movie_id)
CREATE INDEX idx_showtime_time_movie_id ON showtime (time, movie_id);

ANALYZE;
