-- Заполнение Фильмов
into films
select generated.id,
       concat('film ', generated.id) as title,
       random(1, 10.00)              as raiting,
       (enum_range(null::enum_films_limitation))[random(1,4)] as limitation
from generate_series(1, 1000) as generated (id);


-- Заполнение Залов
insert into halls
select generated.id,
       (enum_range(null::enum_halls_type))[random(1,4)] as type,
	concat('hall ',generated.id) as title
from generate_series(1, 10) as generated (id);


-- Заполнение Пользователей
insert into users
select generated.id,
       random_str(50),
       random_str(20),
       random_str(11)
from generate_series(1, 10000) as generated(id);


-- Заполнение Мест
insert into places
select row_number() OVER () as id, generated.hall_id,
       (enum_range(null::enum_places_type))[random(1,4)] as type,
add_row_number.row_number,
add_number.number
from generate_series(1, 10) as generated (hall_id)
    cross join (select generated.row_number from generate_series(1, 10) as generated (row_number)) add_row_number
    left join (select
    generated.hall_id, number
    from generate_series(1, 10) as generated (hall_id)
    cross join (select generated.number from generate_series(1, 10) as generated (number))) add_number
on generated.hall_id = add_number.hall_id;


-- Заполнение Сессий
insert into sessions
select generated.id,
       random(1, 15) as film_id,
       random(1, 5)  as hall_id,
       now() + random(-25.000, 50.000) * '1 days'::interval as begin_at
from generate_series(1, 1000000) as generated(id);


-- Заполнение Заказов
insert into orders
select generated.id,
       random(1, 10000)                                  as user_id,
       CONCAT(random_str(15), '_', random(10000, 99999)) as number,
       random_str(10)                                    as status
from generate_series(1, 1000000) as generated(id);


-- Заполнение Билетов
insert into tickets
select row_number()              OVER () as id, S.id as session_id,
       P.id                   as place_id,
       random(1, 1000)        as order_id,
       random(150.00, 500.00) as price
from sessions S
         join halls H on S.hall_id = H.id
         join places P on P.hall_id = H.id
         join (select *
               from generate_series(1, 11000) as generated(session_id)
                        cross join (select generated.place_id,
                                           random(0, 1) used_place
                                    from generate_series(1, 1000) as generated(place_id)) as add_place_id
               where used_place = 1) Places_Tickets
              on Places_Tickets.session_id = S.id and P.id = Places_Tickets.place_id;


-- Заполнение Платежей
insert into payments
select generated.id,
       generated.id as order_id,
       (array['payed', 'declined', 'in work'])[random(1,3)] as status,
random(300.00, 1000.00) as amount,
random_str(5) as method,
now() + random(-25.000, 50.000) * '1 days'::interval as payed_at
from generate_series(1, 1000000) as generated (id);


-- Выбор всех фильмов на сегодня
explain
analyze
select distinct
on (F.title)
    F.title
from sessions S
    join films F
on F.id = S.film_id
where S.begin_at:: date = now():: date;


-- Подсчёт проданных билетов за неделю
explain
analyze
select count(T.id) as sold_tickects
from tickets T
         join orders O on O.id = T.order_id
         join payments P on O.id = P.order_id and P.status = 'payed'
         join sessions S
              on S.id = T.session_id and (S.begin_at between now()::date - '7 day'::interval and now()::date);


-- Формирование афиши (фильмы, которые показывают сегодня)
explain
analyze
select F.title,
       F.raiting,
       F.limitation,
       count(F.id) as show_number
from sessions S
         join films F on F.id = S.film_id
where S.begin_at::date = now()::date
group by F.id;


-- Поиск 3 самых прибыльных фильмов за неделю
explain
analyze
select F.title,
       sum(P.amount) as amount_payments_for_7_days
from payments P
         join orders O on O.id = P.order_id
         join tickets T on O.id = T.order_id
         join sessions S on S.id = T.session_id and (S.begin_at between now()::date - '7 day'::interval and now()::date)
         join films F on F.id = S.film_id
where P.status = 'payed'
group by F.id
order by amount_payments_for_7_days desc limit 3;


-- Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
explain
analyze
select S.id         as session_id,
       H.title      as hall_title,
       P.row_number as place_row_number,
       P.number     as place_number,
       case
           when T.id is not null then true
           else false
           end      as is_busy
from sessions S
         join halls H on H.id = S.hall_id
         join places P on H.id = P.hall_id
         left join tickets T on P.id = T.place_id and S.id = T.session_id
where S.id = 3;


-- Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс
explain
analyze
select session_id,
       min(price) as min_price,
       max(price) as max_price
from tickets
where session_id = 3
group by session_id;


-- отсортированный список (15 значений) самых больших по размеру объектов БД (таблицы, включая индексы, сами индексы)
select relname, relpages
from pg_class
order by relpages desc LIMIT 15;


-- отсортированные списки (по 5 значений) самых часто и редко используемых индексов
select T.relname as table_name,
       I.relname as index_name,
       S.idx_scan,
       S.idx_tup_read,
       S.idx_tup_fetch
from pg_stat_user_indexes S
         join
     pg_index X on S.indexrelid = X.indexrelid
         join
     pg_class T on X.indrelid = T.oid
         join
     pg_class I on S.indexrelid = I.oid
order by S.idx_scan asc limit 5