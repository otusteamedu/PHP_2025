-- Оптимизация для запросов, фильтрующих сеансы по дате.
-- Ускоряет запросы №1 и №3.
CREATE INDEX idx_showtime_starts_at_utc_date ON showtime (CAST((starts_at AT TIME ZONE 'UTC') AS date));


-- Оптимизация для запросов, фильтрующих проданные билеты по дате.
-- Ускоряет запросы №2 и №4.
-- Создаем покрывающий индекс специально для Запроса №4.
-- Так как просто составной индекс по sold_at и status не дал
-- существенного прироста в запросе №4
CREATE INDEX idx_ticket_covering_q4 ON ticket (sold_at, status) INCLUDE (showtime_id, price_paid);
