-- ДЗ-9 «Индексирование данных»
-- Индексы, добавленные по результатам анализа планов на 10 млн строк.
--
-- Постгрес НЕ создаёт индексы на FK автоматически - все внешние ключи,
-- по которым идут фильтры и JOIN, нужно индексировать вручную.

-- сеансы на сегодня (запросы 1 и 3): фильтр по диапазону start_time
CREATE INDEX idx_screenings_start_time ON screenings (start_time);

-- сеансы конкретного фильма (JOIN в запросах 1 и 3)
CREATE INDEX idx_screenings_movie_id ON screenings (movie_id);

-- зал сеанса (подзапрос в запросе 5)
CREATE INDEX idx_screenings_hall_id ON screenings (hall_id);

-- места зала (запрос 5)
CREATE INDEX idx_seats_hall_id ON seats (hall_id);

-- билеты за неделю (запросы 2 и 4) + покрывающие колонки для Index Only Scan
CREATE INDEX idx_tickets_sold_at ON tickets (sold_at) INCLUDE (screening_id, final_price);

-- цены билетов сеанса (запросы 5 и 6): covering index вместо Seq Scan
CREATE INDEX idx_tickets_screening_final_price
    ON tickets (screening_id) INCLUDE (final_price);
