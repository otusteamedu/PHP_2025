/* схема зала на конкретный сеанс */
EXPLAIN ANALYZE
SELECT
    T.row_number, T.seat_number,
    T.price,
    CASE
        WHEN T.order_id>0
            THEN 'Занято'
        ELSE 'Свободно'
        END AS ticket_status
FROM
    tickets T
        JOIN session S ON S.id = T.session_id
        LEFT JOIN orders ORD ON T.order_id = ORD.id

WHERE
        S.id = 6951
ORDER BY
    T.row_number::integer,
    T.seat_number::integer;

/*
10k записей без индексов

Sort  (cost=198.52..198.53 rows=6 width=51) (actual time=0.553..0.554 rows=6 loops=1)
"  Sort Key: ((t.row_number)::integer), ((t.seat_number)::integer)"
  Sort Method: quicksort  Memory: 25kB
  ->  Nested Loop  (cost=0.29..198.44 rows=6 width=51) (actual time=0.217..0.544 rows=6 loops=1)
        ->  Index Only Scan using session_pkey on session s  (cost=0.29..4.30 rows=1 width=4) (actual time=0.024..0.025 rows=1 loops=1)
              Index Cond: (id = 6951)
              Heap Fetches: 0
        ->  Seq Scan on tickets t  (cost=0.00..194.00 rows=6 width=19) (actual time=0.190..0.514 rows=6 loops=1)
              Filter: (session_id = 6951)
              Rows Removed by Filter: 9994
Planning Time: 0.167 ms
Execution Time: 0.581 ms


10kk записей без индексов

Sort  (cost=122212.02..122212.02 rows=2 width=51) (actual time=172.155..179.099 rows=0 loops=1)
"  Sort Key: ((t.row_number)::integer), ((t.seat_number)::integer)"
  Sort Method: quicksort  Memory: 25kB
  ->  Gather  (cost=1000.43..122212.01 rows=2 width=51) (actual time=172.148..179.090 rows=0 loops=1)
        Workers Planned: 2
        Workers Launched: 2
        ->  Nested Loop  (cost=0.43..121211.81 rows=1 width=51) (actual time=155.614..155.615 rows=0 loops=3)
              ->  Parallel Seq Scan on tickets t  (cost=0.00..121203.33 rows=1 width=19) (actual time=155.613..155.613 rows=0 loops=3)
                    Filter: (session_id = 6951)
                    Rows Removed by Filter: 3333333
              ->  Index Only Scan using session_pkey on session s  (cost=0.43..8.45 rows=1 width=4) (never executed)
                    Index Cond: (id = 6951)
                    Heap Fetches: 0
Planning Time: 0.133 ms
JIT:
  Functions: 15
"  Options: Inlining false, Optimization false, Expressions true, Deforming true"
"  Timing: Generation 1.269 ms (Deform 0.392 ms), Inlining 0.000 ms, Optimization 0.662 ms, Emission 13.503 ms, Total 15.433 ms"
Execution Time: 179.577 ms


10kk записей с индексами

Sort  (cost=20.98..20.98 rows=2 width=51) (actual time=0.057..0.058 rows=0 loops=1)
"  Sort Key: ((t.row_number)::integer), ((t.seat_number)::integer)"
  Sort Method: quicksort  Memory: 25kB
  ->  Nested Loop  (cost=0.87..20.97 rows=2 width=51) (actual time=0.053..0.053 rows=0 loops=1)
        ->  Index Only Scan using session_pkey on session s  (cost=0.43..8.45 rows=1 width=4) (actual time=0.042..0.043 rows=1 loops=1)
              Index Cond: (id = 6951)
              Heap Fetches: 0
"        ->  Index Scan using ""idx-tickets-session_id"" on tickets t  (cost=0.43..12.47 rows=2 width=19) (actual time=0.009..0.009 rows=0 loops=1)"
              Index Cond: (session_id = 6951)
Planning Time: 0.159 ms
Execution Time: 0.087 ms


Execution Time: 0.581 ms
Execution Time: 179.577 ms
Execution Time: 0.087 ms

Выигрыш в 2000 раз

*/