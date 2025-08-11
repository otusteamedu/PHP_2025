<<<<<<<<<Результаты запросов при 10000 рандомных данных>>>>>>>>

1. Выбор всех фильмов на сегодня
!)Без индекса
Seq Scan on films  (cost=0.00..164.04 rows=10004 width=20) (actual time=0.008..0.690 rows=10004 loops=1)
Planning Time: 0.060 ms
Execution Time: 1.044 ms

!)После добавления индекса на id стала быстрее выборка
Seq Scan on films  (cost=0.00..164.04 rows=10004 width=20) (actual time=0.006..0.635 rows=10004 loops=1)
Planning Time: 0.036 ms
Execution Time: 0.980 ms

2. Подсчёт проданных билетов за неделю
!)без индекса
Aggregate  (cost=300.52..300.53 rows=1 width=8) (actual time=1.319..1.320 rows=1 loops=1)
  ->  Seq Scan on orders  (cost=0.00..299.18 rows=538 width=4) (actual time=0.007..1.289 rows=537 loops=1)
        Filter: ((created_at <= now()) AND (created_at >= (now() - '7 days'::interval)))
        Rows Removed by Filter: 9471
Planning Time: 0.344 ms
Execution Time: 1.346 ms
!)с индексом на created_at уменьшилось
Aggregate  (cost=97.22..97.23 rows=1 width=8) (actual time=0.313..0.333 rows=1 loops=1)
  ->  Bitmap Heap Scan on orders  (cost=9.80..95.88 rows=537 width=4) (actual time=0.060..0.275 rows=537 loops=1)
        Recheck Cond: ((created_at >= (now() - '7 days'::interval)) AND (created_at <= now()))
        Heap Blocks: exact=74
        ->  Bitmap Index Scan on idx_orders_created_at  (cost=0.00..9.66 rows=537 width=0) (actual time=0.049..0.050 rows=537 loops=1)
              Index Cond: ((created_at >= (now() - '7 days'::interval)) AND (created_at <= now()))
Planning Time: 0.407 ms
Execution Time: 0.368 ms

3. Формирование афиши (фильмы, которые показывают сегодня)
Unique  (cost=738.35..738.36 rows=1 width=20) (actual time=83.174..90.817 rows=992 loops=1)
Planning Time: 1.985 ms
Execution Time: 91.090 ms
здесь уже используются индексы

4. Поиск 3 самых прибыльных фильмов за неделю
без индекса Execution Time: 185.890 ms
 создам индекс на created_at
Limit  (cost=179.72..179.73 rows=3 width=24) (actual time=0.693..0.694 rows=3 loops=1)
Planning Time: 0.267 ms
Execution Time: 0.722 ms

5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
Sort  (cost=10000001226.97..10000001251.99 rows=10007 width=44) (actual time=52.992..52.992 rows=10007 loops=1)
Planning Time: 0.158 ms
Execution Time: 82.147 ms
Большое время выполнения
при добавлении индекса увеличилось сильно CREATE INDEX idx ON hall(id);
Sort  (cost=10000001226.97..10000001251.99 rows=10007 width=44) (actual time=131.901..132.271 rows=10007 loops=1)
      удалю его
при добавлении индекса на CREATE INDEX idx_session_id ON orders(session_id);
увеличалась произвлдительность
Sort  (cost=1072.22..1097.24 rows=10007 width=44) (actual time=4.709..5.183 rows=10007 loops=1)
при добавлении индекса CREATE INDEX idx_place_id ON orders(place_id);
ситуация не изменилась, его стоит убрать

6. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс
Result  (cost=20000000378.47..20000000378.47 rows=1 width=32) (actual time=119.940..119.942 rows=1 loops=1)
Planning Time: 0.326 ms
Execution Time: 133.355 ms
добавление индекса
CREATE INDEX idx_price ON session_seat_price(price);
Result  (cost=46.18..46.19 rows=1 width=32) (actual time=0.333..0.335 rows=1 loops=1)
Planning Time: 0.370 ms
Execution Time: 0.358 ms
Уменьшилось время

<<<<<<<<<<<Рузультаты запросов на 10000000 данных>>>>>>>>>>
1. Выбор всех фильмов на сегодня
Seq Scan on films  (cost=0.00..163858.04 rows=10010004 width=21) (actual time=0.194..1370.948 rows=10010004 loops=1)
Planning Time: 0.054 ms
Execution Time: 1801.005 ms
оптимизировать можно добавив исловие WHERE release_year = 2025 и индекс по нему
вот такие значения тогда
Bitmap Heap Scan on films  (cost=11639.53..88460.58 rows=1045044 width=21) (actual time=66.859..1259.270 rows=1001190 loops=1)
Planning Time: 0.175 ms
Execution Time: 1260.523 ms

2. Подсчёт проданных билетов за неделю
Finalize Aggregate  (cost=310085.16..310085.17 rows=1 width=8) (actual time=4611.660..4640.750 rows=1 loops=1)
Planning Time: 3.299 ms
Execution Time: 4649.062 ms
можно оптимизировать как у меня было now можно указать конкретные даты
Finalize Aggregate  (cost=261917.65..261917.66 rows=1 width=8) (actual time=1861.368..1861.368 rows=1 loops=1)
Planning Time: 0.128 ms
Execution Time: 993.678 ms

3. Формирование афиши (фильмы, которые показывают сегодня)
Unique  (cost=490298.74..490333.45 rows=280 width=21) (actual time=22056.198..23391.866 rows=2344427 loops=1)
Planning Time: 15.044 ms
Execution Time: 19809.067 ms
Можно CURRENT_DATE заменить на конкретную дату
Можно вместо всей выборки только выбирать id
Вместо выборки по name можно сделать по id и по нему индекс сделать
результат
Unique  (cost=752452.47..965651.58 rows=1792094 width=4) (actual time=6074.256..7266.906 rows=2344427 loops=1)
Planning Time: 0.610 ms
Execution Time: 7272.869 ms

4. Поиск 3 самых прибыльных фильмов за неделю
Limit  (cost=512327.34..512327.35 rows=3 width=25) (actual time=4873.545..4879.579 rows=3 loops=1)
Planning Time: 1.362 ms
Execution Time: 4883.515 ms
Оставить выборку только по id
результат
Вместо таких выражений now() - interval '1 week' AND now() конкретные даты
Limit  (cost=408524.13..408524.14 rows=3 width=12) (actual time=994.552..994.552 rows=3 loops=1)
Planning Time: 0.251 ms
Execution Time: 1025.201 ms

5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс
Gather Merge  (cost=871111.44..1844389.62 rows=8341802 width=44) (actual time=6864.152..8874.979 rows=10010181 loops=1)
Planning Time: 4.352 ms
Execution Time: 9197.818 ms
добавлен индекс на place_id
Gather Merge  (cost=742816.94..1716095.12 rows=8341802 width=44) (actual time=3125.746..5777.065 rows=10010181 loops=1)
Planning Time: 0.228 ms
Execution Time: 6480.353 ms

6. Вывести диапазон миниальной и максимальной цены за билет на конкретный сеанс
Result  (cost=6848.82..6848.83 rows=1 width=32) (actual time=48.239..48.241 rows=1 loops=1)
Planning Time: 3.456 ms
Execution Time: 4.677 ms
тут хороший результат