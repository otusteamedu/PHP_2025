-- Выбор всех фильмов на сегодня

select distinct (m.title) from sessions s
left join movies m on s.movies_id = m.movies_id
where s.start_time::DATE = CURRENT_DATE;

-- Подсчёт проданных билетов за неделю

select count(*) from tickets t 
where t.status = 'sold'
and t.update_status_dt <= CURRENT_DATE - 7 and t.update_status_dt > CURRENT_DATE

-- Формирование афиши (фильмы, которые показывают сегодня)

select m.title, s.start_time, h."name" from sessions s
left join movies m on s.movies_id = m.movies_id
left join halls h on h.halls_id = s.halls_id 
where s.start_time::DATE = CURRENT_DATE
order by m.title;

-- Поиск 3 самых прибыльных фильмов за неделю

select sum(final_price) as final_sum, title from tickets t
left join sessions s on s.sessions_id = t.sessions_id 
left join movies m on m.movies_id = s.movies_id 
where t.status = 'sold'
and t.update_status_dt > CURRENT_DATE - 7 and t.update_status_dt <= CURRENT_DATE
group by title
order by final_sum desc
limit 3; 

-- Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

select z."name", s2."row", s2.seat, 
CASE 
      WHEN t.status = 'sold' THEN '❌'  -- Занято (продано)
      WHEN t.status = 'booked' THEN '⚠️' -- Забронировано
      ELSE '✅'  -- Свободно
END AS status_symbol
from sessions s 
left join halls h on s.halls_id = h.halls_id 
left join seats s2 on s2.halls_id = h.halls_id 
left join zones z on z.zones_id  = s2.zones_id 
left join tickets t on t.sessions_id = s.sessions_id and t.seats_id = s2.seats_id 
where s.sessions_id = 1 -- Укажите нужный ID сеанса
order by s2."row", s2.seat 

-- Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс

SELECT 
    MIN(t.final_price) AS min_price,
    MAX(t.final_price) AS max_price,
    MAX(t.final_price) - MIN(t.final_price) AS price_range
FROM tickets t
WHERE t.sessions_id = 1;  -- Укажите нужный ID сеанса