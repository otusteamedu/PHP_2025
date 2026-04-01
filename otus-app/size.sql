-- 15 самых больших объектов (таблица + индексы, индексы отдельно)
table,ticket,187 MB
table,session,165 MB
table,payment,150 MB
table,seat,134 MB
table,customer,125 MB
table,hall,110 MB
table,cinema,102 MB
table,paymentmethod,87 MB
table,movie,87 MB
table,seattype,87 MB
index,idx_ticket_status_updated_at,30 MB
index,idx_session_movie_id,25 MB
index,idx_payment_payment_method_id,24 MB
index,idx_session_hall_id,24 MB
index,idx_seat_hall_id,24 MB

-- 5 самых часто используемых индексов
hall_pkey (pk)
movie_pkey (pk)
seat_pkey (pk)
seattype_pkey (pk)
session_pkey (pk)

-- 5 редко используемых индексов
-- не используются, тк запросов на них не было:
ticket_pkey (fk)
idx_ticket_payment_id (fk)
idx_payment_customer_id (fk)
idx_payment_payment_method_id (fk)
idx_hall_cinema_id (fk)