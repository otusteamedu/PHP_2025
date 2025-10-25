-- Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс

EXPLAIN ANALYZE
SELECT
    MIN(price) as min_price,
    MAX(price) as max_price
FROM
    seat_prices
WHERE
    session_id = 70;

/*
10k records
QUERY PLAN                                                                                                 |
-----------------------------------------------------------------------------------------------------------+
Aggregate  (cost=189.01..189.02 rows=1 width=64) (actual time=1.492..1.493 rows=1 loops=1)                 |
  ->  Seq Scan on seat_prices  (cost=0.00..189.00 rows=2 width=5) (actual time=0.455..1.488 rows=1 loops=1)|
        Filter: (session_id = 70)                                                                          |
        Rows Removed by Filter: 9999                                                                       |
Planning Time: 0.910 ms                                                                                    |
Execution Time: 1.511 ms                                                                                   |
*/

/*
10kk records without indexes
QUERY PLAN                                                                                                                      |
--------------------------------------------------------------------------------------------------------------------------------+
Aggregate  (cost=58889.88..58889.89 rows=1 width=64) (actual time=273.753..280.971 rows=1 loops=1)                              |
  ->  Gather  (cost=1000.00..58889.87 rows=2 width=5) (actual time=273.581..280.956 rows=2 loops=1)                             |
        Workers Planned: 2                                                                                                      |
        Workers Launched: 2                                                                                                     |
        ->  Parallel Seq Scan on seat_prices  (cost=0.00..57889.67 rows=1 width=5) (actual time=216.456..236.838 rows=1 loops=3)|
              Filter: (session_id = 70)                                                                                         |
              Rows Removed by Filter: 1666666                                                                                   |
Planning Time: 0.674 ms                                                                                                         |
Execution Time: 280.994 ms                                                                                                      |                                                                                                  |                                                                                                 |
*/

/*
10kk records with indexes
QUERY PLAN                                                                                                                                     |
-----------------------------------------------------------------------------------------------------------------------------------------------+
Aggregate  (cost=12.48..12.49 rows=1 width=64) (actual time=0.100..0.100 rows=1 loops=1)                                                       |
  ->  Index Scan using "idx-seat_prices-session_id" on seat_prices  (cost=0.43..12.47 rows=2 width=5) (actual time=0.069..0.088 rows=2 loops=1)|
        Index Cond: (session_id = 70)                                                                                                          |
Planning Time: 1.122 ms                                                                                                                        |
Execution Time: 0.126 ms                                                                                                                       |
*/