-- Поиск 3 самых прибыльных фильмов за неделю

EXPLAIN ANALYZE
SELECT
    title,
    SUM(tickets.price) AS profit
FROM films
    JOIN sessions
        ON films.id = sessions.film_id
    JOIN tickets
        ON sessions.id = tickets.session_id
    JOIN orders
        ON orders.id = tickets.order_id
WHERE orders.status = 'paid'
    AND DATE(orders.paid_at) >= CURRENT_DATE - INTERVAL '1 week'
    AND DATE(orders.paid_at) < CURRENT_DATE
GROUP BY
    films.id
ORDER BY
    profit DESC
    LIMIT 3;

/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000 строк
------------------------------------------------------------------------------------------------------------------------
Limit  (cost=589.91..589.92 rows=3 width=45) (actual time=2.175..2.181 rows=3 loops=1)
  ->  Sort  (cost=589.91..589.95 rows=17 width=45) (actual time=2.174..2.178 rows=3 loops=1)
        Sort Key: (sum(tickets.price)) DESC
        Sort Method: top-N heapsort  Memory: 25kB
        ->  GroupAggregate  (cost=589.35..589.69 rows=17 width=45) (actual time=2.144..2.165 rows=37 loops=1)
              Group Key: films.id
              ->  Sort  (cost=589.35..589.39 rows=17 width=18) (actual time=2.127..2.133 rows=37 loops=1)
                    Sort Key: films.id
                    Sort Method: quicksort  Memory: 26kB
                    ->  Nested Loop  (cost=384.78..589.00 rows=17 width=18) (actual time=0.839..2.114 rows=37 loops=1)
                          ->  Nested Loop  (cost=384.50..580.26 rows=17 width=9) (actual time=0.828..1.919 rows=37 loops=1)
                                ->  Hash Join  (cost=384.21..574.47 rows=17 width=9) (actual time=0.800..1.740 rows=37 loops=1)
                                      Hash Cond: (tickets.order_id = orders.id)
                                      ->  Seq Scan on tickets  (cost=0.00..164.00 rows=10000 width=13) (actual time=0.005..0.422 rows=10000 loops=1)
                                      ->  Hash  (cost=384.00..384.00 rows=17 width=4) (actual time=0.768..0.769 rows=31 loops=1)
                                            Buckets: 1024  Batches: 1  Memory Usage: 10kB
                                            ->  Seq Scan on orders  (cost=0.00..384.00 rows=17 width=4) (actual time=0.052..0.763 rows=31 loops=1)
                                                  Filter: ((status = 'paid'::enum_order_status) AND (date(paid_at) < CURRENT_DATE) AND (date(paid_at) >= (CURRENT_DATE - '7 days'::interval)))
                                                  Rows Removed by Filter: 9969
                                ->  Index Scan using sessions_pkey on sessions  (cost=0.29..0.34 rows=1 width=8) (actual time=0.004..0.004 rows=1 loops=37)
                                      Index Cond: (id = tickets.session_id)
                          ->  Index Scan using films_pkey on films  (cost=0.29..0.51 rows=1 width=13) (actual time=0.005..0.005 rows=1 loops=37)
                                Index Cond: (id = sessions.film_id)
Planning Time: 0.446 ms
Execution Time: 2.236 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк
------------------------------------------------------------------------------------------------------------------------
Limit  (cost=368335.39..368335.40 rows=3 width=48) (actual time=5013.695..5017.502 rows=3 loops=1)
  ->  Sort  (cost=368335.39..368377.41 rows=16807 width=48) (actual time=5004.672..5008.479 rows=3 loops=1)
        Sort Key: (sum(tickets.price)) DESC
        Sort Method: top-N heapsort  Memory: 25kB
        ->  Finalize GroupAggregate  (cost=366046.33..368118.17 rows=16807 width=48) (actual time=4978.764..5003.878 rows=32158 loops=1)
              Group Key: films.id
              ->  Gather Merge  (cost=366046.33..367803.03 rows=14006 width=48) (actual time=4978.743..4991.115 rows=32212 loops=1)
                    Workers Planned: 2
                    Workers Launched: 2
                    ->  Partial GroupAggregate  (cost=365046.31..365186.37 rows=7003 width=48) (actual time=4962.888..4968.451 rows=10737 loops=3)
                          Group Key: films.id
                          ->  Sort  (cost=365046.31..365063.82 rows=7003 width=21) (actual time=4962.850..4963.731 rows=10748 loops=3)
                                Sort Key: films.id
                                Sort Method: quicksort  Memory: 808kB
                                Worker 0:  Sort Method: quicksort  Memory: 801kB
                                Worker 1:  Sort Method: quicksort  Memory: 804kB
                                ->  Nested Loop  (cost=208630.91..364599.04 rows=7003 width=21) (actual time=524.474..4958.221 rows=10748 loops=3)
                                      ->  Nested Loop  (cost=208630.47..344583.43 rows=7003 width=9) (actual time=524.136..3030.768 rows=10748 loops=3)
                                            ->  Parallel Hash Join  (cost=208630.04..325044.77 rows=7003 width=9) (actual time=523.852..1327.162 rows=10748 loops=3)
                                                  Hash Cond: (tickets.order_id = orders.id)
                                                  ->  Parallel Seq Scan on tickets  (cost=0.00..105466.30 rows=4170830 width=13) (actual time=0.339..383.010 rows=3336667 loops=3)
                                                  ->  Parallel Hash  (cost=208542.50..208542.50 rows=7003 width=4) (actual time=523.215..523.216 rows=10756 loops=3)
                                                        Buckets: 32768  Batches: 1  Memory Usage: 1568kB
                                                        ->  Parallel Seq Scan on orders  (cost=0.00..208542.50 rows=7003 width=4) (actual time=5.085..518.788 rows=10756 loops=3)
                                                              Filter: ((status = 'paid'::enum_order_status) AND (date(paid_at) < CURRENT_DATE) AND (date(paid_at) >= (CURRENT_DATE - '7 days'::interval)))
                                                              Rows Removed by Filter: 3325911
                                            ->  Index Scan using sessions_pkey on sessions  (cost=0.43..2.79 rows=1 width=8) (actual time=0.158..0.158 rows=1 loops=32244)
                                                  Index Cond: (id = tickets.session_id)
                                      ->  Index Scan using films_pkey on films  (cost=0.43..2.86 rows=1 width=16) (actual time=0.179..0.179 rows=1 loops=32244)
                                            Index Cond: (id = sessions.film_id)
Planning Time: 3.273 ms
JIT:
  Functions: 85
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 4.286 ms (Deform 1.049 ms), Inlining 0.000 ms, Optimization 1.455 ms, Emission 22.259 ms, Total 28.000 ms"
Execution Time: 5019.035 ms
------------------------------------------------------------------------------------------------------------------------
*/


/*
------------------------------------------------------------------------------------------------------------------------
План на БД до 10000000 строк после индексации
------------------------------------------------------------------------------------------------------------------------
Limit  (cost=188658.90..188658.91 rows=3 width=48) (actual time=584.974..588.909 rows=3 loops=1)
  ->  Sort  (cost=188658.90..188700.92 rows=16807 width=48) (actual time=575.554..579.488 rows=3 loops=1)
        Sort Key: (sum(tickets.price)) DESC
        Sort Method: top-N heapsort  Memory: 25kB
        ->  Finalize GroupAggregate  (cost=186369.84..188441.68 rows=16807 width=48) (actual time=551.681..575.283 rows=32158 loops=1)
              Group Key: films.id
              ->  Gather Merge  (cost=186369.84..188126.54 rows=14006 width=48) (actual time=551.672..563.293 rows=32215 loops=1)
                    Workers Planned: 2
                    Workers Launched: 2
                    ->  Partial GroupAggregate  (cost=185369.82..185509.88 rows=7003 width=48) (actual time=533.931..538.872 rows=10738 loops=3)
                          Group Key: films.id
                          ->  Sort  (cost=185369.82..185387.33 rows=7003 width=21) (actual time=533.893..534.519 rows=10748 loops=3)
                                Sort Key: films.id
                                Sort Method: quicksort  Memory: 817kB
                                Worker 0:  Sort Method: quicksort  Memory: 798kB
                                Worker 1:  Sort Method: quicksort  Memory: 798kB
                                ->  Nested Loop  (cost=678.45..184922.55 rows=7003 width=21) (actual time=13.768..531.064 rows=10748 loops=3)
                                      ->  Nested Loop  (cost=678.01..164906.94 rows=7003 width=9) (actual time=13.712..397.332 rows=10748 loops=3)
                                            ->  Nested Loop  (cost=677.58..145368.28 rows=7003 width=9) (actual time=13.680..293.740 rows=10748 loops=3)
                                                  ->  Parallel Bitmap Heap Scan on orders  (cost=677.14..76837.07 rows=7003 width=4) (actual time=13.415..173.224 rows=10756 loops=3)
                                                        Recheck Cond: ((date(paid_at) >= (CURRENT_DATE - '7 days'::interval)) AND (date(paid_at) < CURRENT_DATE))
                                                        Filter: (status = 'paid'::enum_order_status)
                                                        Rows Removed by Filter: 21506
                                                        Heap Blocks: exact=19734
"                                                        ->  Bitmap Index Scan on ""idx-orders-paid_at_date""  (cost=0.00..672.94 rows=50050 width=0) (actual time=17.190..17.190 rows=96787 loops=1)"
                                                              Index Cond: ((date(paid_at) >= (CURRENT_DATE - '7 days'::interval)) AND (date(paid_at) < CURRENT_DATE))
"                                                  ->  Index Scan using ""idx-tickets-order_id"" on tickets  (cost=0.43..9.77 rows=2 width=13) (actual time=0.008..0.011 rows=1 loops=32268)"
                                                        Index Cond: (order_id = orders.id)
                                            ->  Index Scan using sessions_pkey on sessions  (cost=0.43..2.79 rows=1 width=8) (actual time=0.009..0.009 rows=1 loops=32244)
                                                  Index Cond: (id = tickets.session_id)
                                      ->  Index Scan using films_pkey on films  (cost=0.43..2.86 rows=1 width=16) (actual time=0.012..0.012 rows=1 loops=32244)
                                            Index Cond: (id = sessions.film_id)
Planning Time: 1.090 ms
JIT:
  Functions: 82
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 4.707 ms (Deform 1.332 ms), Inlining 0.000 ms, Optimization 1.455 ms, Emission 22.158 ms, Total 28.320 ms"
Execution Time: 590.749 ms
------------------------------------------------------------------------------------------------------------------------
*/
