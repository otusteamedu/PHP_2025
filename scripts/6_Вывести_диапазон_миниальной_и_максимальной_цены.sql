explain select session_movie_id, max(actual_price), min(actual_price) from ticket
join session_movie sm on ticket.session_movie_id = sm.id
group by session_movie_id
limit 1;

-- 10.000
--     HashAggregate  (cost=233.29..243.02 rows=973 width=68)
--      Group Key: ticket.session_movie_id
--   ->  Hash Semi Join  (cost=3.89..225.58 rows=1028 width=10)
--         Hash Cond: (ticket.session_movie_id = session_movie.id)
--         ->  Seq Scan on ticket  (cost=0.00..184.00 rows=10000 width=10)
--         ->  Hash  (cost=2.64..2.64 rows=100 width=4)
--               ->  Limit  (cost=0.00..1.64 rows=100 width=4)
--                     ->  Seq Scan on session_movie  (cost=0.00..164.00 rows=10000 width=4)

-- 10.000.000
-- Limit  (cost=66.97..119.06 rows=100 width=68)
--        ->  GroupAggregate  (cost=66.97..854147.75 rows=1639837 width=68)
--         Group Key: ticket.session_movie_id
--         ->  Merge Join  (cost=66.97..762749.23 rows=10000020 width=10)
--               Merge Cond: (ticket.session_movie_id = sm.id)
--               ->  Index Scan using idx_ticket_session_movie_id on ticket  (cost=0.43..582562.41 rows=10000020 width=10)
--               ->  Index Only Scan using session_movie_pkey on session_movie sm  (cost=0.43..259684.43 rows=10000000 width=4)
-- Execution Time: 0.183 ms
-- Оптимизация не требуется
