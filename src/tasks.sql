--1. Выбор всех фильмов на сегодня
select m.name
from cinema.session as s
join cinema.movie as m on m.id = s.movie_id
where s.start_time >= current_date and  s.start_time < current_date + interval '1 day';

--2. Подсчёт проданных билетов за неделю
select count(t.id)
from cinema.ticket as t
join cinema.orders as o on o.id = t.order_id
where o.created_at >= date_trunc('week', now()) and  o.created_at < date_trunc('week', now()) + interval '1 week'

--3. Формирование афиши (фильмы, которые показывают сегодня)

--4. Поиск 3 самых прибыльных фильмов за неделю

--5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

--6. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс