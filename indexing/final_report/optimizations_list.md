## Перечень оптимизаций с пояснениями

### Созданные индексы

1. `idx_screening_show_date`

```sql
CREATE INDEX idx_screening_show_date ON screening (show_date);
```
- **Назначение:** Фильтрация по дате показа (запросы 1, 3)
- **Эффект:** Bitmap Index Scan вместо Seq Scan
- **Использование:** 2 сканирования, 410 кортежей
- **Результат:** Улучшение в 3-5.7 раза

2. `idx_ticket_purchase_time`  

```sql
CREATE INDEX idx_ticket_purchase_time ON ticket (purchase_time);
````
- **Назначение:** Фильтрация по времени покупки (запросы 2, 4)
- **Эффект:** Index Only Scan вместо Parallel Seq Scan
- **Использование:** 3 сканирования, 4.3M кортежей
- **Результат:** Улучшение запроса 2 в 3.7 раза

3. `idx\_ticket\_screening\_id`

```sql
CREATE INDEX idx_ticket_screening_id ON ticket (screening_id);
```
- **Назначение:** Оптимизация JOIN операций (запросы 4, 5)
- **Эффект:** Ускорение связывания ticket ↔ screening
- **Использование:** 3 сканирования, 59 кортежей
- **Результат:** Улучшение запроса 5 в 2 раза

4. `idx\_screening\_movie\_id`

```sql
CREATE INDEX idx_screening_movie_id ON screening (movie_id);
```
- **Назначение:** JOIN с таблицей movie (запросы 1, 3, 4)
- **Эффект:** Ускорение связывания screening ↔ movie
- **Использование:** 6 сканирований, 6 кортежей
- **Результат:** Общее улучшение JOIN операций

5. `idx\_screening\_hall\_id`

```sql
CREATE INDEX idx_screening_hall_id ON screening (hall_id);
```
- **Назначение:** JOIN с таблицей hall (запросы 5, 6)
- **Эффект:** Ускорение связывания screening ↔ hall
- **Использование:** 8 сканирований, 8 кортежей
- **Результат:** Улучшение запроса 5

6. `idx\_screening\_show\_date\_time (составной)`

```sql
CREATE INDEX idx_screening_show_date_time ON screening (show_date, show_time);
```
- **Назначение:** Фильтрация + сортировка (запрос 3)
- **Эффект:** Планировщик не использует
- **Использование:** 0 сканирований
- **Результат:** Не используется

7. `idx\_ticket\_purchase\_screening\_price (составной)`

```sql
CREATE INDEX idx_ticket_purchase_screening_price ON ticket (purchase_time, screening_id, price);
```
- **Назначение:** Покрытие полей запроса 4
- **Эффект:** Планировщик не использует
- **Размер:** 329 MB (самый большой индекс)
- **Использование:** 0 сканирований
- **Результат:** Не используется, занимает много места

---

#### Статистика использования индексов

** Самые активные (TOP‑5):**

* `ticket_screening_id_seat_id_key` – 9 437 215 сканирований
* `screening_pkey` – 8 578 227 сканирований
* `seat_pkey` – 8 578 214 сканирований
* `customer_pkey` – 8 578 202 сканирований
* `hall_pkey` – 306 621 сканирований

** Неиспользуемые (TOP‑5):**

* `customer_email_key` – 0 сканирований
* `idx_screening_show_date_time` – 0 сканирований
* `idx_ticket_purchase_screening_price` – 0 сканирований
* `ticket_pkey` – 0 сканирований
* `idx_screening_show_date` – 2 сканирования (редко)

---

#### Анализ размеров объектов БД

** 15 самых больших объектов:**

1. `ticket` (table) – 1325 MB (488 MB данные + 836 MB индексы)
2. `idx_ticket_purchase_screening_price` – 329 MB (не используется)
3. `ticket_screening_id_seat_id_key` – 214 MB (активно используется)
4. `ticket_pkey` – 182 MB (не используется)
5. `idx_ticket_screening_id` – 56 MB (используется)
6. `idx_ticket_purchase_time` – 55 MB (используется)
7. `customer` (table) – 38 MB
8. `screening` (table) – 23 MB
9. `customer_email_key` – 14 MB (не используется)
10. `seat` (table) – 8272 kB
11. `screening_hall_id_show_date_show_time_key` – 5240 kB
12. `customer_pkey` – 4408 kB (активно используется)
13. `screening_pkey` – 3312 kB (активно используется)
14. `idx_screening_show_date_time` – 3224 kB (не используется)
15. `seat_hall_id_row_number_seat_number_key` – 2464 kB

---

#### Выводы

Анализ выявил **332 MB неиспользуемых индексов** (0 сканирований), которые создают overhead без пользы, которые можно удалить:

```sql
DROP INDEX idx_ticket_purchase_screening_price; -- Экономия 329 MB
DROP INDEX idx_screening_show_date_time;        -- Экономия 3.2 MB
```
