/* самые прибыльные фильмы за неделю */
EXPLAIN ANALYZE
SELECT F.title, SUM(T.price) as profit FROM films F
        JOIN session S ON F.id = S.film_id
        JOIN tickets T ON S.id = T.session_id
        JOIN orders ORD ON ORD.id = T.order_id
WHERE
        ORD.status = 'paid'
  AND ORD.created_at >= CURRENT_DATE - INTERVAL '7 days'
  AND ORD.created_at < CURRENT_DATE + INTERVAL '1 day'
GROUP BY
    F.id
ORDER BY
    profit DESC
    LIMIT 3;

/*
10k записей без индексов

Limit  (cost=580.71..580.71 rows=3 width=87) (actual time=2.809..2.813 rows=3 loops=1)
  ->  Sort  (cost=580.71..580.77 rows=24 width=87) (actual time=2.808..2.811 rows=3 loops=1)
        Sort Key: (sum(t.price)) DESC
        Sort Method: top-N heapsort  Memory: 25kB
        ->  GroupAggregate  (cost=579.92..580.40 rows=24 width=87) (actual time=2.744..2.784 rows=52 loops=1)
              Group Key: f.id
              ->  Sort  (cost=579.92..579.98 rows=24 width=60) (actual time=2.564..2.570 rows=52 loops=1)
                    Sort Key: f.id
                    Sort Method: quicksort  Memory: 29kB
                    ->  Nested Loop  (cost=367.87..579.37 rows=24 width=60) (actual time=1.216..2.550 rows=52 loops=1)
                          ->  Nested Loop  (cost=367.59..570.82 rows=24 width=9) (actual time=1.210..2.438 rows=52 loops=1)
                                ->  Hash Join  (cost=367.30..562.56 rows=24 width=9) (actual time=1.201..2.312 rows=52 loops=1)
                                      Hash Cond: (t.order_id = ord.id)
                                      ->  Seq Scan on tickets t  (cost=0.00..169.00 rows=10000 width=13) (actual time=0.009..0.467 rows=10000 loops=1)
                                      ->  Hash  (cost=367.00..367.00 rows=24 width=4) (actual time=1.185..1.186 rows=54 loops=1)
                                            Buckets: 1024  Batches: 1  Memory Usage: 10kB
                                            ->  Seq Scan on orders ord  (cost=0.00..367.00 rows=24 width=4) (actual time=0.030..1.177 rows=54 loops=1)
                                                  Filter: (((status)::text = 'paid'::text) AND (date(created_at) <= CURRENT_DATE) AND (date(created_at) >= (CURRENT_DATE - '7 days'::interval)))
                                                  Rows Removed by Filter: 9946
                                ->  Index Scan using session_pkey on session s  (cost=0.29..0.34 rows=1 width=8) (actual time=0.002..0.002 rows=1 loops=52)
                                      Index Cond: (id = t.session_id)
                          ->  Index Scan using films_pkey on films f  (cost=0.29..0.36 rows=1 width=55) (actual time=0.002..0.002 rows=1 loops=52)
                                Index Cond: (id = s.film_id)
Planning Time: 0.483 ms
Execution Time: 2.859 ms


10kk записей без индексов
Limit  (cost=328803.38..328803.39 rows=3 width=87) (actual time=1664.656..1673.730 rows=3 loops=1)
  ->  Sort  (cost=328803.38..328866.26 rows=25152 width=87) (actual time=1650.281..1659.354 rows=3 loops=1)
        Sort Key: (sum(t.price)) DESC
        Sort Method: top-N heapsort  Memory: 25kB
        ->  Finalize GroupAggregate  (cost=325377.79..328478.30 rows=25152 width=87) (actual time=1595.335..1650.796 rows=49142 loops=1)
              Group Key: f.id
              ->  Gather Merge  (cost=325377.79..328006.70 rows=20960 width=87) (actual time=1595.315..1627.075 rows=49307 loops=1)
                    Workers Planned: 2
                    Workers Launched: 2
                    ->  Partial GroupAggregate  (cost=324377.77..324587.37 rows=10480 width=87) (actual time=1578.732..1592.301 rows=16436 loops=3)
                          Group Key: f.id
                          ->  Sort  (cost=324377.77..324403.97 rows=10480 width=60) (actual time=1578.686..1581.265 rows=16463 loops=3)
                                Sort Key: f.id
                                Sort Method: quicksort  Memory: 2094kB
                                Worker 0:  Sort Method: quicksort  Memory: 1652kB
                                Worker 1:  Sort Method: quicksort  Memory: 1650kB
                                ->  Nested Loop  (cost=191499.87..323677.95 rows=10480 width=60) (actual time=428.275..1572.330 rows=16463 loops=3)
                                      ->  Nested Loop  (cost=191499.43..318388.58 rows=10480 width=9) (actual time=428.236..1393.068 rows=16463 loops=3)
                                            ->  Parallel Hash Join  (cost=191499.00..313223.17 rows=10480 width=9) (actual time=428.179..1209.092 rows=16463 loops=3)
                                                  Hash Cond: (t.order_id = ord.id)
                                                  ->  Parallel Seq Scan on tickets t  (cost=0.00..110786.67 rows=4166667 width=13) (actual time=0.038..210.357 rows=3333333 loops=3)
                                                  ->  Parallel Hash  (cost=191368.00..191368.00 rows=10480 width=4) (actual time=427.654..427.656 rows=18286 loops=3)
                                                        Buckets: 65536 (originally 32768)  Batches: 1 (originally 1)  Memory Usage: 2976kB
                                                        ->  Parallel Seq Scan on orders ord  (cost=0.00..191368.00 rows=10480 width=4) (actual time=8.704..413.502 rows=18286 loops=3)
                                                              Filter: (((status)::text = 'paid'::text) AND (date(created_at) <= CURRENT_DATE) AND (date(created_at) >= (CURRENT_DATE - '7 days'::interval)))
                                                              Rows Removed by Filter: 3315047
                                            ->  Index Scan using session_pkey on session s  (cost=0.43..0.49 rows=1 width=8) (actual time=0.011..0.011 rows=1 loops=49389)
                                                  Index Cond: (id = t.session_id)
                                      ->  Index Scan using films_pkey on films f  (cost=0.43..0.50 rows=1 width=55) (actual time=0.010..0.010 rows=1 loops=49389)
                                            Index Cond: (id = s.film_id)
Planning Time: 0.569 ms
JIT:
  Functions: 85
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 3.229 ms (Deform 1.291 ms), Inlining 0.000 ms, Optimization 1.550 ms, Emission 38.930 ms, Total 43.709 ms"
Execution Time: 1674.966 ms


10kk записей с индексами
Limit  (cost=300585.04..300585.05 rows=3 width=87) (actual time=1094.308..1103.447 rows=3 loops=1)
  ->  Sort  (cost=300585.04..300647.92 rows=25152 width=87) (actual time=1080.107..1089.244 rows=3 loops=1)
        Sort Key: (sum(t.price)) DESC
        Sort Method: top-N heapsort  Memory: 25kB
        ->  Finalize GroupAggregate  (cost=297159.45..300259.95 rows=25152 width=87) (actual time=1023.962..1080.872 rows=49142 loops=1)
              Group Key: f.id
              ->  Gather Merge  (cost=297159.45..299788.35 rows=20960 width=87) (actual time=1023.922..1056.908 rows=49312 loops=1)
                    Workers Planned: 2
                    Workers Launched: 2
                    ->  Partial GroupAggregate  (cost=296159.43..296369.03 rows=10480 width=87) (actual time=989.623..1003.368 rows=16437 loops=3)
                          Group Key: f.id
                          ->  Sort  (cost=296159.43..296185.63 rows=10480 width=60) (actual time=989.555..992.315 rows=16463 loops=3)
                                Sort Key: f.id
                                Sort Method: quicksort  Memory: 2151kB
                                Worker 0:  Sort Method: quicksort  Memory: 1615kB
                                Worker 1:  Sort Method: quicksort  Memory: 1630kB
                                ->  Nested Loop  (cost=1.30..295459.61 rows=10480 width=60) (actual time=8.548..982.412 rows=16463 loops=3)
                                      ->  Nested Loop  (cost=0.87..290170.24 rows=10480 width=9) (actual time=8.517..773.572 rows=16463 loops=3)
                                            ->  Nested Loop  (cost=0.43..285004.82 rows=10480 width=9) (actual time=8.482..571.607 rows=16463 loops=3)
                                                  ->  Parallel Seq Scan on orders ord  (cost=0.00..191368.00 rows=10480 width=4) (actual time=8.398..427.140 rows=18286 loops=3)
                                                        Filter: (((status)::text = 'paid'::text) AND (date(created_at) <= CURRENT_DATE) AND (date(created_at) >= (CURRENT_DATE - '7 days'::interval)))
                                                        Rows Removed by Filter: 3315047
"                                                  ->  Index Scan using ""idx-tickets-order_id"" on tickets t  (cost=0.43..8.91 rows=2 width=13) (actual time=0.006..0.008 rows=1 loops=54858)"
                                                        Index Cond: (order_id = ord.id)
                                            ->  Index Scan using session_pkey on session s  (cost=0.43..0.49 rows=1 width=8) (actual time=0.012..0.012 rows=1 loops=49389)
                                                  Index Cond: (id = t.session_id)
                                      ->  Index Scan using films_pkey on films f  (cost=0.43..0.50 rows=1 width=55) (actual time=0.012..0.012 rows=1 loops=49389)
                                            Index Cond: (id = s.film_id)
Planning Time: 0.679 ms
JIT:
  Functions: 70
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 4.062 ms (Deform 1.171 ms), Inlining 0.000 ms, Optimization 1.581 ms, Emission 37.694 ms, Total 43.337 ms"
Execution Time: 1104.543 ms


Execution Time: 2.859 ms
Execution Time: 1674.966 ms
Execution Time: 1104.543 ms

Выигрыш в 1,5 раз

*/