-- добавляем фк-индексы, тк они не создаются автоматически
CREATE INDEX idx_ticket_payment_id ON public.ticket(paymentid);
CREATE INDEX idx_ticket_seat_id ON public.ticket(seatid);
CREATE INDEX idx_ticket_session_id ON public.ticket(sessionid);
CREATE INDEX idx_session_hall_id ON public.session(hallid);
CREATE INDEX idx_session_movie_id ON public.session(movieid);
CREATE INDEX idx_seat_hall_id ON public.seat(hallid);
CREATE INDEX idx_seat_seat_type_id ON public.seat(seattypeid);
CREATE INDEX idx_payment_customer_id ON public.payment(customerid);
CREATE INDEX idx_payment_payment_method_id ON public.payment(paymentmethodid);
CREATE INDEX idx_hall_cinema_id ON public.hall(cinemaid);

-- 1. Выбор всех фильмов на сегодня
explain analyze
select m.name
from public.session s
         join public.movie m on s.movieId = m.id
where s.startsAt >= current_date
group by m.name
;

-- x10000
-- HashAggregate  (cost=381.97..383.97 rows=200 width=516) (actual time=1.450..1.452 rows=0 loops=1)
--   Group Key: m.name
--   Batches: 1  Memory Usage: 40kB
--   ->  Hash Join  (cost=110.46..373.12 rows=3541 width=516) (actual time=1.446..1.447 rows=0 loops=1)
--         Hash Cond: (s.movieid = m.id)
--         ->  Seq Scan on session s  (cost=0.00..253.33 rows=3541 width=4) (actual time=1.445..1.445 rows=0 loops=1)
--               Filter: (startsat >= CURRENT_DATE)
--               Rows Removed by Filter: 10000
--         ->  Hash  (cost=95.76..95.76 rows=1176 width=520) (never executed)
--               ->  Seq Scan on movie m  (cost=0.00..95.76 rows=1176 width=520) (never executed)
-- Planning Time: 1.966 ms
-- Execution Time: 1.579 ms

-- x1000000
-- Group  (cost=16950.06..16960.18 rows=100 width=11) (actual time=164.580..167.587 rows=7 loops=1)
--   Group Key: m.name
--   ->  Gather Merge  (cost=16950.06..16959.97 rows=84 width=11) (actual time=164.580..167.585 rows=7 loops=1)
--         Workers Planned: 2
--         Workers Launched: 2
--         ->  Group  (cost=15950.04..15950.25 rows=42 width=11) (actual time=125.902..125.904 rows=2 loops=3)
--               Group Key: m.name
--               ->  Sort  (cost=15950.04..15950.14 rows=42 width=11) (actual time=125.901..125.902 rows=2 loops=3)
--                     Sort Key: m.name
--                     Sort Method: quicksort  Memory: 25kB
--                     Worker 0:  Sort Method: quicksort  Memory: 25kB
--                     Worker 1:  Sort Method: quicksort  Memory: 25kB
--                     ->  Nested Loop  (cost=0.42..15948.91 rows=42 width=11) (actual time=59.187..125.796 rows=2 loops=3)
--                           ->  Parallel Seq Scan on session s  (cost=0.00..15596.00 rows=42 width=4) (actual time=56.832..122.806 rows=2 loops=3)
--                                 Filter: (startsat >= CURRENT_DATE)
--                                 Rows Removed by Filter: 333331
--                           ->  Index Scan using movie_pkey on movie m  (cost=0.42..8.40 rows=1 width=15) (actual time=1.275..1.275 rows=1 loops=7)
--                                 Index Cond: (id = s.movieid)
-- Planning Time: 18.545 ms
-- Execution Time: 167.739 ms

-- добавляем индекс для s.startsAt
CREATE INDEX idx_session_start_at ON public.session(startsAt);

-- видим, что actual time и cost значенительно уменьшился
-- Group  (cost=115.92..115.96 rows=9 width=11) (actual time=3.369..3.375 rows=7 loops=1)
--   Group Key: m.name
--   ->  Sort  (cost=115.92..115.94 rows=9 width=11) (actual time=3.367..3.368 rows=7 loops=1)
--         Sort Key: m.name
--         Sort Method: quicksort  Memory: 25kB
--         ->  Nested Loop  (cost=4.92..115.78 rows=9 width=11) (actual time=1.304..3.340 rows=7 loops=1)
--               ->  Bitmap Heap Scan on session s  (cost=4.50..39.79 rows=9 width=4) (actual time=0.203..0.496 rows=7 loops=1)
--                     Recheck Cond: (startsat >= CURRENT_DATE)
--                     Heap Blocks: exact=7
--                     ->  Bitmap Index Scan on idx_session_start_at  (cost=0.00..4.50 rows=9 width=0) (actual time=0.050..0.050 rows=7 loops=1)
--                           Index Cond: (startsat >= CURRENT_DATE)
--               ->  Index Scan using movie_pkey on movie m  (cost=0.42..8.44 rows=1 width=15) (actual time=0.404..0.404 rows=1 loops=7)
--                     Index Cond: (id = s.movieid)
-- Planning Time: 7.131 ms
-- Execution Time: 3.410 ms

-- 2. Подсчёт проданных билетов за неделю

explain analyze
select count(t.id)
from public.ticket t
where t.status in ('finished', 'paid')
  and t.updatedAt >= (now() - interval '1 week')
;

-- x10000
-- Aggregate  (cost=199.97..199.98 rows=1 width=8) (actual time=3.711..3.712 rows=1 loops=1)
--   ->  Seq Scan on ticket t  (cost=0.00..199.92 rows=19 width=4) (actual time=0.038..3.697 rows=61 loops=1)
-- "        Filter: (((status)::text = ANY ('{finished,paid}'::text[])) AND (updatedat >= (now() - '7 days'::interval)))"
--         Rows Removed by Filter: 9939
-- Planning Time: 2.990 ms
-- Execution Time: 3.768 ms

-- x1000000
-- Finalize Aggregate  (cost=17674.30..17674.31 rows=1 width=8) (actual time=121.257..123.548 rows=1 loops=1)
--   ->  Gather  (cost=17674.09..17674.30 rows=2 width=8) (actual time=121.253..123.545 rows=3 loops=1)
--         Workers Planned: 2
--         Workers Launched: 2
--         ->  Partial Aggregate  (cost=16674.09..16674.10 rows=1 width=8) (actual time=103.526..103.526 rows=1 loops=3)
--               ->  Parallel Seq Scan on ticket t  (cost=0.00..16667.33 rows=2702 width=4) (actual time=0.448..103.125 rows=2173 loops=3)
-- "                    Filter: (((status)::text = ANY ('{finished,paid}'::text[])) AND (updatedat >= (now() - '7 days'::interval)))"
--                     Rows Removed by Filter: 331161
-- Planning Time: 6.886 ms
-- Execution Time: 123.584 ms

-- добавляем индекс для t.updatedAt и t.status
CREATE INDEX idx_ticket_status_updated_at ON public.ticket(status, updatedAt);

-- видим, что actual time и cost значенительно уменьшился
-- Aggregate  (cost=8507.77..8507.78 rows=1 width=8) (actual time=9.865..9.867 rows=1 loops=1)
--   ->  Bitmap Heap Scan on ticket t  (cost=171.30..8491.57 rows=6481 width=4) (actual time=1.798..9.223 rows=6513 loops=1)
-- "        Recheck Cond: (((status)::text = ANY ('{finished,paid}'::text[])) AND (updatedat >= (now() - '7 days'::interval)))"
--         Heap Blocks: exact=4501
--         ->  Bitmap Index Scan on idx_ticket_status_updated_at  (cost=0.00..169.67 rows=6481 width=0) (actual time=1.098..1.099 rows=6513 loops=1)
-- "              Index Cond: (((status)::text = ANY ('{finished,paid}'::text[])) AND (updatedat >= (now() - '7 days'::interval)))"
-- Planning Time: 0.239 ms
-- Execution Time: 9.925 ms

-- 3. Формирование афиши (фильмы, которые показывают сегодня)
explain analyze
select m.name, s.startsAt
from public.session s
         join public.movie m on s.movieId = m.id
where s.startsAt >= current_date
order by startsAt desc;

-- x10000
-- Sort  (cost=581.86..590.71 rows=3541 width=524) (actual time=2.903..2.907 rows=0 loops=1)
--   Sort Key: s.startsat DESC
--   Sort Method: quicksort  Memory: 25kB
--   ->  Hash Join  (cost=110.46..373.12 rows=3541 width=524) (actual time=2.860..2.864 rows=0 loops=1)
--         Hash Cond: (s.movieid = m.id)
--         ->  Seq Scan on session s  (cost=0.00..253.33 rows=3541 width=12) (actual time=2.859..2.859 rows=0 loops=1)
--               Filter: (startsat >= CURRENT_DATE)
--               Rows Removed by Filter: 10000
--         ->  Hash  (cost=95.76..95.76 rows=1176 width=520) (never executed)
--               ->  Seq Scan on movie m  (cost=0.00..95.76 rows=1176 width=520) (never executed)
-- Planning Time: 0.185 ms
-- Execution Time: 2.949 ms

-- x1000000
-- Gather Merge  (cost=16950.06..16959.86 rows=84 width=19) (actual time=110.066..113.100 rows=7 loops=1)
--   Workers Planned: 2
--   Workers Launched: 2
--   ->  Sort  (cost=15950.04..15950.14 rows=42 width=19) (actual time=92.442..92.443 rows=2 loops=3)
--         Sort Key: s.startsat DESC
--         Sort Method: quicksort  Memory: 25kB
--         Worker 0:  Sort Method: quicksort  Memory: 25kB
--         Worker 1:  Sort Method: quicksort  Memory: 25kB
--         ->  Nested Loop  (cost=0.42..15948.91 rows=42 width=19) (actual time=25.283..91.692 rows=2 loops=3)
--               ->  Parallel Seq Scan on session s  (cost=0.00..15596.00 rows=42 width=12) (actual time=25.254..91.641 rows=2 loops=3)
--                     Filter: (startsat >= CURRENT_DATE)
--                     Rows Removed by Filter: 333331
--               ->  Index Scan using movie_pkey on movie m  (cost=0.42..8.40 rows=1 width=15) (actual time=0.017..0.017 rows=1 loops=7)
--                     Index Cond: (id = s.movieid)
-- Planning Time: 0.228 ms
-- Execution Time: 113.151 ms

-- ранее уже добавлен индекс для s.startsAt, для join индекс уже есть
-- видим, что actual time и cost также значенительно уменьшился
-- Nested Loop  (cost=0.85..116.57 rows=9 width=19) (actual time=0.056..0.173 rows=7 loops=1)
--   ->  Index Scan Backward using idx_session_start_at on session s  (cost=0.43..40.58 rows=9 width=12) (actual time=0.028..0.064 rows=7 loops=1)
--         Index Cond: (startsat >= CURRENT_DATE)
--   ->  Index Scan using movie_pkey on movie m  (cost=0.42..8.44 rows=1 width=15) (actual time=0.014..0.014 rows=1 loops=7)
--         Index Cond: (id = s.movieid)
-- Planning Time: 0.583 ms
-- Execution Time: 0.206 ms

-- 4. Поиск 3 самых прибыльных фильмов за неделю
explain analyze
select m.id,
       m.name,
       sum(s.defaultPrice * st.priceModifier) / (100 * 100) as totalSum
from public.movie m
         join public.session s on m.id = s.movieId
         join public.ticket t on s.id = t.sessionId
         join public.seat s2 on t.seatId = s2.id
         join public.seatType st on st.id = s2.seatTypeId
where t.status in ('finished', 'paid')
  and t.updatedAt >= (now() - interval '1 week')
group by m.id
order by totalSum desc limit 3
;

-- x10000
-- Limit  (cost=612.35..612.36 rows=3 width=552) (actual time=8.277..8.282 rows=3 loops=1)
--   ->  Sort  (cost=612.35..612.40 rows=19 width=552) (actual time=8.276..8.280 rows=3 loops=1)
--         Sort Key: ((sum((s.defaultprice * st.pricemodifier)) / '10000'::numeric)) DESC
--         Sort Method: top-N heapsort  Memory: 25kB
--         ->  GroupAggregate  (cost=611.63..612.10 rows=19 width=552) (actual time=8.184..8.229 rows=61 loops=1)
--               Group Key: m.id
--               ->  Sort  (cost=611.63..611.68 rows=19 width=532) (actual time=8.166..8.172 rows=61 loops=1)
--                     Sort Key: m.id
--                     Sort Method: quicksort  Memory: 29kB
--                     ->  Nested Loop  (cost=201.28..611.22 rows=19 width=532) (actual time=3.274..8.103 rows=61 loops=1)
--                           ->  Nested Loop  (cost=201.00..603.76 rows=19 width=532) (actual time=3.206..7.392 rows=61 loops=1)
--                                 ->  Nested Loop  (cost=200.72..466.06 rows=19 width=532) (actual time=3.147..6.660 rows=61 loops=1)
--                                       ->  Nested Loop  (cost=200.44..459.64 rows=19 width=16) (actual time=3.064..5.933 rows=61 loops=1)
--                                             ->  Hash Join  (cost=200.16..321.89 rows=19 width=8) (actual time=3.010..5.472 rows=61 loops=1)
--                                                   Hash Cond: (p.id = t.paymentid)
--                                                   ->  Seq Scan on payment p  (cost=0.00..115.36 rows=1236 width=4) (actual time=0.020..1.630 rows=10000 loops=1)
--                                                   ->  Hash  (cost=199.92..199.92 rows=19 width=12) (actual time=2.960..2.960 rows=61 loops=1)
--                                                         Buckets: 1024  Batches: 1  Memory Usage: 11kB
--                                                         ->  Seq Scan on ticket t  (cost=0.00..199.92 rows=19 width=12) (actual time=0.028..2.927 rows=61 loops=1)
-- "                                                              Filter: (((status)::text = ANY ('{finished,paid}'::text[])) AND (updatedat >= (now() - '7 days'::interval)))"
--                                                               Rows Removed by Filter: 9939
--                                             ->  Index Scan using session_pkey on session s  (cost=0.29..7.25 rows=1 width=16) (actual time=0.007..0.007 rows=1 loops=61)
--                                                   Index Cond: (id = t.sessionid)
--                                       ->  Index Scan using movie_pkey on movie m  (cost=0.28..0.34 rows=1 width=520) (actual time=0.011..0.011 rows=1 loops=61)
--                                             Index Cond: (id = s.movieid)
--                                 ->  Index Scan using seat_pkey on seat s2  (cost=0.28..7.25 rows=1 width=8) (actual time=0.011..0.011 rows=1 loops=61)
--                                       Index Cond: (id = t.seatid)
--                           ->  Index Scan using seattype_pkey on seattype st  (cost=0.28..0.39 rows=1 width=8) (actual time=0.011..0.011 rows=1 loops=61)
--                                 Index Cond: (id = s2.seattypeid)
-- Planning Time: 5.199 ms
-- Execution Time: 8.439 ms

-- x1000000
-- Limit  (cost=54475.95..54475.95 rows=3 width=47) (actual time=1744.062..1745.439 rows=3 loops=1)
--   ->  Sort  (cost=54475.95..54492.16 rows=6485 width=47) (actual time=1744.061..1745.437 rows=3 loops=1)
--         Sort Key: ((sum((s.defaultprice * st.pricemodifier)) / '10000'::numeric)) DESC
--         Sort Method: top-N heapsort  Memory: 25kB
--         ->  Finalize GroupAggregate  (cost=53569.77..54392.13 rows=6485 width=47) (actual time=1737.304..1744.890 rows=6487 loops=1)
--               Group Key: m.id
--               ->  Gather Merge  (cost=53569.77..54254.32 rows=5404 width=47) (actual time=1737.286..1743.272 rows=6511 loops=1)
--                     Workers Planned: 2
--                     Workers Launched: 2
--                     ->  Partial GroupAggregate  (cost=52569.75..52630.54 rows=2702 width=47) (actual time=1717.860..1718.416 rows=2170 loops=3)
--                           Group Key: m.id
--                           ->  Sort  (cost=52569.75..52576.50 rows=2702 width=27) (actual time=1717.852..1717.961 rows=2173 loops=3)
--                                 Sort Key: m.id
--                                 Sort Method: quicksort  Memory: 192kB
--                                 Worker 0:  Sort Method: quicksort  Memory: 188kB
--                                 Worker 1:  Sort Method: quicksort  Memory: 272kB
--                                 ->  Nested Loop  (cost=2.12..52415.74 rows=2702 width=27) (actual time=7.249..1716.398 rows=2173 loops=3)
--                                       ->  Nested Loop  (cost=1.70..46645.24 rows=2702 width=31) (actual time=6.067..1580.752 rows=2173 loops=3)
--                                             ->  Nested Loop  (cost=1.27..45329.88 rows=2702 width=31) (actual time=5.075..1136.772 rows=2173 loops=3)
--                                                   ->  Nested Loop  (cost=0.85..31777.96 rows=2702 width=31) (actual time=3.692..830.342 rows=2173 loops=3)
--                                                         ->  Nested Loop  (cost=0.42..30462.58 rows=2702 width=20) (actual time=3.488..375.766 rows=2173 loops=3)
--                                                               ->  Parallel Seq Scan on ticket t  (cost=0.00..16667.33 rows=2702 width=12) (actual time=1.174..56.649 rows=2173 loops=3)
-- "                                                                    Filter: (((status)::text = ANY ('{finished,paid}'::text[])) AND (updatedat >= (now() - '7 days'::interval)))"
--                                                                     Rows Removed by Filter: 331161
--                                                               ->  Index Scan using session_pkey on session s  (cost=0.42..5.11 rows=1 width=16) (actual time=0.146..0.146 rows=1 loops=6518)
--                                                                     Index Cond: (id = t.sessionid)
--                                                         ->  Index Scan using movie_pkey on movie m  (cost=0.42..0.49 rows=1 width=15) (actual time=0.208..0.208 rows=1 loops=6518)
--                                                               Index Cond: (id = s.movieid)
--                                                   ->  Index Scan using seat_pkey on seat s2  (cost=0.42..5.02 rows=1 width=8) (actual time=0.140..0.140 rows=1 loops=6518)
--                                                         Index Cond: (id = t.seatid)
--                                             ->  Index Scan using seattype_pkey on seattype st  (cost=0.42..0.49 rows=1 width=8) (actual time=0.203..0.203 rows=1 loops=6518)
--                                                   Index Cond: (id = s2.seattypeid)
--                                       ->  Index Only Scan using payment_pkey on payment p  (cost=0.42..2.14 rows=1 width=4) (actual time=0.062..0.062 rows=1 loops=6518)
--                                             Index Cond: (id = t.paymentid)
--                                             Heap Fetches: 0
-- Planning Time: 20.543 ms
-- Execution Time: 1745.549 ms

-- ранее уже добавлены индексы для t.updatedAt и t.status, для join индекс уже есть
-- видим, что также уменьшился actual time и cost
-- Limit  (cost=46211.71..46211.72 rows=3 width=47) (actual time=1252.434..1254.139 rows=3 loops=1)
--   ->  Sort  (cost=46211.71..46227.91 rows=6480 width=47) (actual time=1252.432..1254.137 rows=3 loops=1)
--         Sort Key: ((sum((s.defaultprice * st.pricemodifier)) / '10000'::numeric)) DESC
--         Sort Method: top-N heapsort  Memory: 25kB
--         ->  Finalize GroupAggregate  (cost=45306.22..46127.96 rows=6480 width=47) (actual time=1245.667..1253.613 rows=6482 loops=1)
--               Group Key: m.id
--               ->  Gather Merge  (cost=45306.22..45990.26 rows=5400 width=47) (actual time=1245.662..1251.943 rows=6506 loops=1)
--                     Workers Planned: 2
--                     Workers Launched: 2
--                     ->  Partial GroupAggregate  (cost=44306.19..44366.94 rows=2700 width=47) (actual time=1209.336..1209.892 rows=2169 loops=3)
--                           Group Key: m.id
--                           ->  Sort  (cost=44306.19..44312.94 rows=2700 width=27) (actual time=1209.328..1209.429 rows=2171 loops=3)
--                                 Sort Key: m.id
--                                 Sort Method: quicksort  Memory: 245kB
--                                 Worker 0:  Sort Method: quicksort  Memory: 176kB
--                                 Worker 1:  Sort Method: quicksort  Memory: 278kB
--                                 ->  Nested Loop  (cost=173.40..44152.31 rows=2700 width=27) (actual time=10.590..1207.241 rows=2171 loops=3)
--                                       ->  Nested Loop  (cost=172.98..38382.56 rows=2700 width=31) (actual time=9.691..1115.461 rows=2171 loops=3)
--                                             ->  Nested Loop  (cost=172.55..37068.17 rows=2700 width=31) (actual time=7.722..804.619 rows=2171 loops=3)
--                                                   ->  Nested Loop  (cost=172.13..23520.08 rows=2700 width=31) (actual time=5.681..578.868 rows=2171 loops=3)
--                                                         ->  Nested Loop  (cost=171.70..22205.68 rows=2700 width=20) (actual time=3.975..265.851 rows=2171 loops=3)
--                                                               ->  Parallel Bitmap Heap Scan on ticket t  (cost=171.28..8415.93 rows=2700 width=12) (actual time=2.077..31.445 rows=2171 loops=3)
-- "                                                                    Recheck Cond: (((status)::text = ANY ('{finished,paid}'::text[])) AND (updatedat >= (now() - '7 days'::interval)))"
--                                                                     Heap Blocks: exact=1457
--                                                                     ->  Bitmap Index Scan on idx_ticket_status_updated_at  (cost=0.00..169.66 rows=6480 width=0) (actual time=5.284..5.284 rows=6513 loops=1)
-- "                                                                          Index Cond: (((status)::text = ANY ('{finished,paid}'::text[])) AND (updatedat >= (now() - '7 days'::interval)))"
--                                                               ->  Index Scan using session_pkey on session s  (cost=0.42..5.11 rows=1 width=16) (actual time=0.107..0.107 rows=1 loops=6513)
--                                                                     Index Cond: (id = t.sessionid)
--                                                         ->  Index Scan using movie_pkey on movie m  (cost=0.42..0.49 rows=1 width=15) (actual time=0.143..0.143 rows=1 loops=6513)
--                                                               Index Cond: (id = s.movieid)
--                                                   ->  Index Scan using seat_pkey on seat s2  (cost=0.42..5.02 rows=1 width=8) (actual time=0.103..0.103 rows=1 loops=6513)
--                                                         Index Cond: (id = t.seatid)
--                                             ->  Index Scan using seattype_pkey on seattype st  (cost=0.42..0.49 rows=1 width=8) (actual time=0.141..0.141 rows=1 loops=6513)
--                                                   Index Cond: (id = s2.seattypeid)
--                                       ->  Index Only Scan using payment_pkey on payment p  (cost=0.42..2.14 rows=1 width=4) (actual time=0.041..0.041 rows=1 loops=6513)
--                                             Index Cond: (id = t.paymentid)
--                                             Heap Fetches: 0
-- Planning Time: 3.403 ms
-- Execution Time: 1254.219 ms

-- 5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
explain analyze
select case when t.status = 'free' then 'free' else 'booked' end as status,
       s.number,
       s.coordinates
from public.ticket t
         join public.seat s on t.seatId = s.id
where t.sessionId = :sessionId
;

-- x10000
-- Nested Loop  (cost=4.59..28.01 rows=2 width=39) (actual time=0.037..0.038 rows=1 loops=1)
--   ->  Bitmap Heap Scan on ticket t  (cost=4.30..11.40 rows=2 width=9) (actual time=0.017..0.018 rows=1 loops=1)
--         Recheck Cond: (sessionid = 9525)
--         Heap Blocks: exact=1
--         ->  Bitmap Index Scan on idx_ticket_session_id  (cost=0.00..4.30 rows=2 width=0) (actual time=0.010..0.011 rows=1 loops=1)
--               Index Cond: (sessionid = 9525)
--   ->  Index Scan using seat_pkey on seat s  (cost=0.29..8.30 rows=1 width=11) (actual time=0.010..0.010 rows=1 loops=1)
--         Index Cond: (id = t.seatid)
-- Planning Time: 0.466 ms
-- Execution Time: 0.077 ms

-- x1000000
-- Nested Loop  (cost=0.85..29.35 rows=2 width=41) (actual time=0.845..0.976 rows=2 loops=1)
--   ->  Index Scan using idx_ticket_session_id on ticket t  (cost=0.42..12.46 rows=2 width=9) (actual time=0.507..0.540 rows=2 loops=1)
--         Index Cond: (sessionid = 392826)
--   ->  Index Scan using seat_pkey on seat s  (cost=0.42..8.44 rows=1 width=13) (actual time=0.210..0.210 rows=1 loops=2)
--         Index Cond: (id = t.seatid)
-- Planning Time: 0.941 ms
-- Execution Time: 1.005 ms

-- для этого запроса не нужно добавлять доп индексы

-- 6. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс
explain analyze
select
    min(st.priceModifier) * ss.defaultPrice as minSum,
    max(st.priceModifier) * ss.defaultPrice as maxSum
from public.session ss
join public.seat s on s.hallId = ss.hallId
join public.seatType st on s.seatTypeId = st.id
where ss.id = :sessionId
group by ss.id;

-- x10000
-- GroupAggregate  (cost=4.87..20.45 rows=1 width=20) (actual time=0.038..0.039 rows=1 loops=1)
--   Group Key: ss.id
--   ->  Nested Loop  (cost=4.87..20.42 rows=2 width=16) (actual time=0.033..0.034 rows=1 loops=1)
--         ->  Nested Loop  (cost=4.59..19.72 rows=2 width=16) (actual time=0.025..0.027 rows=1 loops=1)
--               ->  Index Scan using session_pkey on session ss  (cost=0.29..8.30 rows=1 width=16) (actual time=0.014..0.015 rows=1 loops=1)
--                     Index Cond: (id = 6114)
--               ->  Bitmap Heap Scan on seat s  (cost=4.30..11.40 rows=2 width=8) (actual time=0.008..0.008 rows=1 loops=1)
--                     Recheck Cond: (hallid = ss.hallid)
--                     Heap Blocks: exact=1
--                     ->  Bitmap Index Scan on idx_seat_hall_id  (cost=0.00..4.30 rows=2 width=0) (actual time=0.004..0.004 rows=1 loops=1)
--                           Index Cond: (hallid = ss.hallid)
--         ->  Index Scan using seattype_pkey on seattype st  (cost=0.29..0.35 rows=1 width=8) (actual time=0.006..0.006 rows=1 loops=1)
--               Index Cond: (id = s.seattypeid)
-- Planning Time: 0.438 ms
-- Execution Time: 0.127 ms

-- x1000000
-- GroupAggregate  (cost=1.27..21.93 rows=1 width=20) (actual time=1.472..1.474 rows=1 loops=1)
--   Group Key: ss.id
--   ->  Nested Loop  (cost=1.27..21.90 rows=2 width=16) (actual time=1.333..1.464 rows=2 loops=1)
--         ->  Nested Loop  (cost=0.85..20.92 rows=2 width=16) (actual time=0.850..0.899 rows=2 loops=1)
--               ->  Index Scan using session_pkey on session ss  (cost=0.42..8.44 rows=1 width=16) (actual time=0.192..0.193 rows=1 loops=1)
--                     Index Cond: (id = 84925)
--               ->  Index Scan using idx_seat_hall_id on seat s  (cost=0.42..12.46 rows=2 width=8) (actual time=0.652..0.698 rows=2 loops=1)
--                     Index Cond: (hallid = ss.hallid)
--         ->  Index Scan using seattype_pkey on seattype st  (cost=0.42..0.49 rows=1 width=8) (actual time=0.279..0.279 rows=1 loops=2)
--               Index Cond: (id = s.seattypeid)
-- Planning Time: 3.591 ms
-- Execution Time: 1.566 ms

-- для этого запроса не нужно добавлять доп индексы

-- общая возможная оптимизация:
-- можно собирать статистику, которая часто используется, в отдельной(ых) таблице(ах) и/или дополнительно кешировать данные
