-- Индекс позволит ускорить 1 и 3 запросы.
-- Добавляем в индекс два поля, т.к. в запросах есть
-- Фильтрация по дате: В запросе есть Filter: ((start_time)::date = '2025-03-15'::date).
-- Используется приведение start_time к дате, поэтому индекс должен быть на выражении (start_time::date)
-- Простой индекс на start_time не будет эффективен из-за преобразования типа

-- Соединение с таблицей movies: Используется Hash Cond: (s.movies_id = m.movies_id)

CREATE INDEX idx_sessions_start_time_movies_id 
ON sessions ((start_time::date), movies_id);

-- Индекс позволит ускорить 2 и 4 запросы.
-- Parallel Seq Scan on tickets с фильтром по status и update_status_dt
-- Делаем частичный индекс только по проданным билетам, т.к. выборка идет только по ним
-- само поле статус хранить в индексе бессмысленно, т.к. оно будет везде одинаково и будет только увеличивать размер индекса

CREATE INDEX idx_tickets_update_dt_sold
ON tickets (update_status_dt)
WHERE status = 'sold';

-- первоначально был создан такой индекс, но планировщик запроса его не брал
--CREATE INDEX idx_tickets_status_update_dt_sold 
--ON tickets (status, update_status_dt) 
--WHERE status = 'sold';

-- Индекс позволит ускорить 5 и 6 запросы.
-- Parallel Seq Scan on tickets с фильтром sessions_id
CREATE INDEX idx_tickets_sessions_id 
ON tickets (sessions_id);