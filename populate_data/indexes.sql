-- Для запроса 1 Все фильмы
-- Индекс по времени начала сеанса, для ускорения поиска по дате начала сеанса 
create index idx_session_start_time on "session" ((start_time::date));
-- Индекс по Foreign Key для ускорения join
create index idx_session_movie_id on "session" (movie_id);


-- Для запроса 2 Бронирования за неделю
-- Индекс по времени создания заказа
create index idx_order_created_at on "order" ((created_at::date));
-- Индекс по Foreign Key для ускорения join
create index idx_booking_order_id on booking (order_id);

-- Для запроса 3 Все фильмы сегодня
-- Дополнительные индексы не нужны, подходят индексы для запроса 1
 
-- Для запроса 4 Самый прибыльный фильм за неделю
-- Индекс по цене бронирования
create index idx_booking_booking_priсe on booking (booking_price);
-- Индекс по Foreign Key для ускорения join
create index idx_booking_session_id on booking (session_id);
-- Составной индекс по бронированию с включением цены
create index idx_booking_session_order_include_booking_price ON booking(session_id, order_id) include (booking_price);

-- Для запроса 5 места
-- Индекс по Foreign Key для ускорения join
create index idx_seat_room_id on seat (room_id);
-- Индекс по паре Foreign Key для ускорения фильтрации
create index idx_booking_session_seat on booking (session_id, seat_id);

-- Для запроса 6 Минимальная/максимальная цена на сеанс
-- Индекс по цене бронирования создан выше

-- Индекс по статусу заказа
create index idx_order_active_status 
on "order"((created_at::date), order_status_id) 
where order_status_id in (2, 4);

