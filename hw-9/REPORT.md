============================================================
1. ФИЛЬМЫ НА СЕГОДНЯ
   ============================================================

10k:
Hash Join
-> Seq Scan (film)
-> Seq Scan (seance)
Execution Time: 8.2 ms

10M:
Nested Loop
-> Parallel Seq Scan (seance)
Filter: seance_time = CURRENT_DATE
-> Index Scan (film_pkey)
Execution Time: 1080.8 ms

10M + оптимизация:
Добавлены индексы, но план не изменился.
Причина — слишком много записей на один день,
селективность низкая, индекс не используется.

Индекс:
CREATE INDEX idx_seance_film_time
ON seance(film_id, seance_time);


============================================================
2. БИЛЕТЫ ЗА НЕДЕЛЮ
   ============================================================

10k:
Aggregate
-> Nested Loop
-> Seq Scan (seance)
-> Index Scan (ticket)
Execution Time: 9.2 ms

10M:
Finalize Aggregate
-> Gather (workers: 2)
-> Partial Aggregate
-> Merge Join
-> Parallel Index Only Scan (ticket, unique_seat_per_seance)
-> Index Scan (seance_pkey)
Filter: seance_time BETWEEN CURRENT_DATE-7 AND CURRENT_DATE
Execution Time: 446.3 ms

10M + оптимизация:
Execution Time: 333.1 ms

Индекс:
CREATE INDEX idx_ticket_seance_id
ON ticket(seance_id);


============================================================
3. АФИША
   ============================================================

10k:
Unique
-> Sort
-> Hash Join
-> Seq Scan (film)
-> Seq Scan (seance)
Execution Time: 5.1 ms

10M:
HashAggregate (group by f.name)
-> Gather (workers: 2)
-> HashAggregate
-> Nested Loop
-> Parallel Seq Scan (seance)
Filter: seance_time = CURRENT_DATE
-> Index Scan (film_pkey)
Execution Time: 648.3 ms

Комментарий:
Попытка переписать запрос без ::date и использовать индекс
ухудшает план. Полный перебор строк за текущий день
оказался самым быстрым вариантом.


============================================================
4. ТОП-3 ФИЛЬМОВ ЗА НЕДЕЛЮ
   ============================================================

10k:
Limit
-> Sort
-> HashAggregate
-> Nested Loop
-> Seq Scan (seance)
-> Index Scan (film)
-> Index Scan (ticket)
Execution Time: 16.2 ms

10M:
Limit
-> Sort (sum(price) DESC)
-> Finalize GroupAggregate
-> Gather Merge (workers: 2)
-> Partial GroupAggregate
-> Nested Loop
-> Nested Loop
-> Parallel Seq Scan (ticket)
Filter: status = 'sold'
-> Index Scan (seance_pkey)
Filter: seance_time BETWEEN CURRENT_DATE-7 AND CURRENT_DATE
-> Index Scan (film_pkey)
Execution Time: 777.3 ms

10M + оптимизация:
Execution Time: 647.5 ms

Индексы:
CREATE INDEX idx_seance_time_film
ON seance(seance_time, film_id);

CREATE INDEX idx_ticket_seance_status
ON ticket(seance_id, status);


============================================================
5. СХЕМА ЗАЛА
   ============================================================

10k:
GroupAggregate
-> Sort
-> Hash Left Join
-> Nested Loop (generate_series)
-> Index Only Scan (seance)
-> generate_series
-> Index Scan (ticket)
Execution Time: 0.7 ms

10M:
Аналогичный план с Index Scan по ticket
Execution Time: 1.03 ms

Вывод:
Запрос работает очень быстро, оптимизация не требуется.


============================================================
6. ДИАПАЗОН ЦЕН
   ============================================================

10k:
Aggregate
-> Index Scan (ticket, unique_seat_per_seance)
Execution Time: 0.1 ms

10M:
Aggregate
-> Index Scan (ticket, unique_seat_per_seance)
Filter: seance_id = 1
Execution Time: 0.10 ms

Вывод:
Запрос оптимален, дополнительных индексов не требуется.


============================================================
ОБЩИЕ ВЫВОДЫ
============================================================

- При низкой селективности (много строк на день) индексы неэффективны
- Parallel Seq Scan часто быстрее, чем Index Scan
- Наибольший эффект дают составные индексы под JOIN + фильтр
- Часть запросов уже находится в оптимальной форме
