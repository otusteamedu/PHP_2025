explain SELECT movie_id, m.name, SUM(actual_price) as sum FROM ticket
    INNER JOIN session_movie sm ON sm.id = ticket.session_movie_id
    INNER JOIN movie m ON sm.movie_id = m.id
WHERE created_at >= NOW() - INTERVAL '7 days' AND created_at <= NOW() AND status = 'куплен'
GROUP BY movie_id, m.name
ORDER BY sum DESC
limit 3;

-- 10.000
-- Limit  (cost=658.62..658.63 rows=3 width=69)
--        ->  Sort  (cost=658.62..660.34 rows=690 width=69)
--         Sort Key: (sum(ticket.actual_price)) DESC
--         ->  HashAggregate  (cost=641.08..649.70 rows=690 width=69)
-- "              Group Key: sm.movie_id, m.name"
--               ->  Hash Join  (cost=314.52..616.08 rows=3333 width=43)
--                     Hash Cond: (sm.movie_id = m.id)
--                     ->  Hash Join  (cost=289.00..581.75 rows=3333 width=10)
--                           Hash Cond: (ticket.session_movie_id = sm.id)
--                           ->  Seq Scan on ticket  (cost=0.00..284.00 rows=3333 width=10)
--                                 Filter: ((created_at)::date > (CURRENT_DATE - 7))
--                           ->  Hash  (cost=164.00..164.00 rows=10000 width=8)
--                                 ->  Seq Scan on session_movie sm  (cost=0.00..164.00 rows=10000 width=8)
--                     ->  Hash  (cost=16.90..16.90 rows=690 width=37)
--                           ->  Seq Scan on movie m  (cost=0.00..16.90 rows=690 width=37)

-- 10.000.000 записей
-- Limit  (cost=174975.99..174976.00 rows=3 width=69)
--        ->  Sort  (cost=174975.99..175177.17 rows=80472 width=69)
--         Sort Key: (sum(ticket.actual_price)) DESC
--         ->  Finalize GroupAggregate  (cost=163764.59..173935.90 rows=80472 width=69)
-- "              Group Key: sm.movie_id, m.name"
--               ->  Gather Merge  (cost=163764.59..172259.40 rows=67060 width=69)
--                     Workers Planned: 2
--                     ->  Partial GroupAggregate  (cost=162764.57..163518.99 rows=33530 width=69)
-- "                          Group Key: sm.movie_id, m.name"
--                           ->  Sort  (cost=162764.57..162848.39 rows=33530 width=43)
-- "                                Sort Key: sm.movie_id, m.name"
--                                 ->  Hash Join  (cost=106019.46..160244.26 rows=33530 width=43)
--                                       Hash Cond: (sm.movie_id = m.id)
--                                       ->  Merge Join  (cost=105630.46..159767.21 rows=33530 width=10)
--                                             Merge Cond: (sm.id = ticket.session_movie_id)
--                                             ->  Parallel Index Scan using session_movie_pkey on session_movie sm  (cost=0.43..265049.10 rows=4166667 width=8)
--                                             ->  Sort  (cost=105629.98..105831.16 rows=80472 width=10)
--                                                   Sort Key: ticket.session_movie_id
--                                                   ->  Bitmap Heap Scan on ticket  (cost=2777.90..99073.04 rows=80472 width=10)
--                                                         Recheck Cond: ((created_at >= (now() - '7 days'::interval)) AND (created_at <= now()) AND ((status)::text = 'куплен'::text))
--                                                         ->  Bitmap Index Scan on idx_ticket_created_at_status  (cost=0.00..2757.78 rows=80472 width=0)
--                                                               Index Cond: ((created_at >= (now() - '7 days'::interval)) AND (created_at <= now()) AND ((status)::text = 'куплен'::text))
--                                       ->  Hash  (cost=264.00..264.00 rows=10000 width=37)
--                                             ->  Seq Scan on movie m  (cost=0.00..264.00 rows=10000 width=37)
-- JIT:
--   Functions: 28
-- "  Options: Inlining false, Optimization false, Expressions true, Deforming true"
-- Execution Time: 1159.256 ms

-- Оптимизация
-- 1) В изначальном варианте было условие created_at >= NOW() - INTERVAL '7 days'. Так как планировщик не видел верхней
--    границы к условию был добавлен AND created_at <= NOW(). После добавления верхней границы планировщик начал
--    использовать индекс  "idx_ticket_created_at_status"
-- 2) Создала индексы на внешние ключи


CREATE INDEX idx_ticket_session_movie_id ON ticket (session_movie_id);
CREATE INDEX idx_ticket_session_movie_movie_id ON session_movie (movie_id);

-- Limit  (cost=167537.74..167537.74 rows=3 width=69)
--        ->  Sort  (cost=167537.74..167722.16 rows=73770 width=69)
--         Sort Key: (sum(ticket.actual_price)) DESC
--         ->  Finalize HashAggregate  (cost=165662.15..166584.27 rows=73770 width=69)
-- "              Group Key: sm.movie_id, m.name"
--               ->  Gather  (cost=158515.56..165047.39 rows=61476 width=69)
--                     Workers Planned: 2
--                     ->  Partial HashAggregate  (cost=157515.56..157899.79 rows=30738 width=69)
-- "                          Group Key: sm.movie_id, m.name"
--                           ->  Hash Join  (cost=103128.99..157285.03 rows=30738 width=43)
--                                 Hash Cond: (sm.movie_id = m.id)
--                                 ->  Merge Join  (cost=102739.99..156815.31 rows=30738 width=10)
--                                       Merge Cond: (sm.id = ticket.session_movie_id)
--                                       ->  Parallel Index Scan using session_movie_pkey on session_movie sm  (cost=0.43..265049.10 rows=4166667 width=8)
--                                       ->  Sort  (cost=102739.52..102923.94 rows=73770 width=10)
--                                             Sort Key: ticket.session_movie_id
--                                             ->  Bitmap Heap Scan on ticket  (cost=2546.89..96774.94 rows=73770 width=10)
--                                                   Recheck Cond: ((created_at >= (now() - '7 days'::interval)) AND (created_at <= now()) AND ((status)::text = 'куплен'::text))
--                                                   ->  Bitmap Index Scan on idx_ticket_created_at_status  (cost=0.00..2528.44 rows=73770 width=0)
--                                                         Index Cond: ((created_at >= (now() - '7 days'::interval)) AND (created_at <= now()) AND ((status)::text = 'куплен'::text))
--                                 ->  Hash  (cost=264.00..264.00 rows=10000 width=37)
--                                       ->  Seq Scan on movie m  (cost=0.00..264.00 rows=10000 width=37)
-- JIT:
--   Functions: 24
-- "  Options: Inlining false, Optimization false, Expressions true, Deforming true"
-- Execution Time: 561.966 ms

-- Стоимость уменьшилось на 7438 ед.
-- Время выполнения уменьшилась в 3 раза