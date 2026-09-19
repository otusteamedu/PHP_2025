# ДЗ «Индексирование данных» (PostgreSQL)

Отчёт оформлен по критериям оценки.

СУБД: PostgreSQL 12.4. Схема кинотеатра с предыдущих занятий (7 таблиц).
Наполнение 10k ≈ 10 100 строк; наполнение 10M ≈ **9 651 053** строки
(`tickets` 8 000 000, `screening_prices` 1 200 000, `screenings` 400 000,
`seats` 50 000, `movies` 1 000, `halls` 50, `seat_categories` 3).

Планы: `EXPLAIN (ANALYZE, BUFFERS, TIMING)`. Полные дампы лежат в `results/`.
Ниже для каждого запроса — SQL, план 10k, план 10M до оптимизаций, план 10M
после (индексы + настройки сервера), что изменилось.

---

## 1. Скрипт создания БД

`sql/01_schema.sql` — схема с ДЗ-6 (категории мест, фильмы, залы, места,
сеансы, цены, билеты).

## 2. Скрипты заполнения тестовыми данными

| Файл | Объём |
|---|---|
| `sql/02_fill_10k.sql` | ~10 100 строк |
| `sql/03_fill_10m.sql` | ~9 651 053 строки |

Оптимизации (не наполнение): `sql/05_indexes.sql`, `sql/06_admin_settings.sql`,
`sql/07_partition_tickets.sql`. Запросы: `sql/04_queries.sql`.

---

## 3. Таблица результатов по 6 запросам

«Простые» (1 таблица): №2, №6. «Сложные» (JOIN / агрегаты): №1, №3, №4, №5.
№4 в формулировке ЛК стоит рядом с простыми, но это JOIN трёх таблиц + `SUM`.

Сводка (Execution Time):

| № | Запрос | 10k | 10M до | 10M после (индексы + настройки сервера) | Ускорение |
|---|---|---|---|---|---|
| 1 | Фильмы на сегодня | 0.829 ms | 131.926 ms | **1.674 ms** | ~79× |
| 2 | Билеты за неделю | 2.226 ms | 1517.565 ms | **279.929 ms** | ~5.4× |
| 3 | Афиша на сегодня | 0.696 ms | 132.176 ms | **4.180 ms** | ~32× |
| 4 | Топ-3 прибыльных | 4.450 ms | 3356.948 ms | **784.142 ms** | ~4.3× |
| 5 | Схема зала сеанса | 0.324 ms | 3.701 ms | **1.105 ms** | ~3.3× |
| 6 | MIN/MAX цены сеанса | 0.053 ms | 0.054 ms | **0.027 ms** | уже был Index Scan |

Колонка «после» — `results/results_10m_admin.txt`: индексы, `VACUUM ANALYZE`,
`shared_buffers=512MB`, `work_mem=64MB`, `random_page_cost=1.1`, `jit=off`.

Сырые файлы: `results/results_10k.txt`, `results/results_10m.txt`,
`results/results_10m_admin.txt`. Промежуточные:
`results_10m_indexed.txt` (только индексы, Seq Scan на №2),
`results_10m_tuned.txt` (session `work_mem`),
`results_10m_partitioned.txt` (RANGE-секции `tickets`).

---

### Запрос 1. Выбор всех фильмов на сегодня

```sql
SELECT DISTINCT m.id, m.title, m.duration_minutes
FROM movies m
         JOIN screenings s ON s.movie_id = m.id
WHERE s.start_time >= date_trunc('day', now())
  AND s.start_time < date_trunc('day', now()) + interval '1 day';
```

**План на БД до 10 000 строк** (`Execution Time: 0.829 ms`):

```
Unique
  -> Sort
       -> Hash Join
            -> Seq Scan on movies m
            -> Hash
                 -> Seq Scan on screenings s
                      Filter: start_time за сегодня
                      Rows Removed by Filter: 790
```

**План на БД до 10 000 000 строк, до оптимизаций** (`131.926 ms`):

```
HashAggregate
  -> Gather
       -> Hash Join
            -> Parallel Seq Scan on screenings s   -- 400k строк, остаётся ~3.3k
                 Filter: start_time за сегодня
                 Rows Removed by Filter: 198334
            -> Hash -> Seq Scan on movies m
Buffers: shared hit=1993 read=651
```

**План на 10 000 000 после улучшений** (`1.674 ms`):

```
HashAggregate
  -> Hash Join
       -> Index Scan using idx_screenings_start_time on screenings s
            Index Cond: start_time за сегодня     -- читаются только ~3333 сеанса
       -> Hash -> Seq Scan on movies m            -- 1000 строк, Seq Scan уместен
Buffers: shared hit=950  (read=0)
```

**Что улучшили:** Seq Scan 400k сеансов → Index Scan по `start_time`. Кэш
держит страницы индекса (`read` исчез).

---

### Запрос 2. Подсчёт проданных билетов за неделю

```sql
SELECT COUNT(*) AS tickets_sold_last_week
FROM tickets
WHERE sold_at >= now() - interval '7 days';
```

**План до 10 000 строк** (`2.226 ms`): `Aggregate → Seq Scan on tickets`.

**План до 10 000 000, до оптимизаций** (`1517.565 ms`):

```
Finalize Aggregate
  -> Gather
       -> Partial Aggregate
            -> Parallel Seq Scan on tickets          -- 8 млн строк
                 Filter: sold_at >= now() - 7 days
                 Rows Removed by Filter: 1250457     -- селективность ~53%
Buffers: shared hit=12068 read=68222              -- ~532 МБ с диска
JIT: Total 37.792 ms
```

**План на 10 000 000 после улучшений** (`279.929 ms`):

```
Finalize Aggregate
  -> Gather
       -> Partial Aggregate
            -> Parallel Index Only Scan using idx_tickets_sold_at
                 Index Cond: sold_at >= now() - 7 days
                 Heap Fetches: 0
Buffers: shared hit=1769220   (read=0, JIT нет)
```

**Что улучшили:** Seq Scan кучи 627 МБ → Index Only Scan покрывающего индекса
309 МБ. Без `INCLUDE` планировщик оставлял Seq Scan (селективность 53%).
`shared_buffers=512MB` убрал `read`; `jit=off` убрал 38 ms компиляции.

После секций (`results_10m_partitioned.txt`, 245 ms): `Parallel Append`,
`Subplans Removed: 2` — июнь и июль не читаются.

---

### Запрос 3. Афиша на сегодня

```sql
SELECT DISTINCT m.title, s.start_time, h.name AS hall
FROM screenings s
         JOIN movies m ON m.id = s.movie_id
         JOIN halls h ON h.id = s.hall_id
WHERE s.start_time >= date_trunc('day', now())
  AND s.start_time < date_trunc('day', now()) + interval '1 day'
ORDER BY s.start_time;
```

**План до 10 000 строк** (`0.696 ms`): Sort + два Hash Join, Seq Scan всех
трёх таблиц.

**План до 10 000 000, до оптимизаций** (`132.176 ms`):

```
Unique → Sort → Gather → Hash Join (halls)
                           → Hash Join (movies)
                                → Parallel Seq Scan on screenings
                                     Filter: start_time за сегодня
                                     Rows Removed by Filter: 198334
```

**План на 10 000 000 после улучшений** (`4.180 ms`):

```
Unique → Sort → Hash Join (halls)          -- Seq Scan, 50 строк
                 → Hash Join (movies)      -- Seq Scan, 1000 строк
                      → Index Scan using idx_screenings_start_time
Buffers: shared hit=954
```

**Что улучшили:** как №1 — индекс по `start_time`. Справочники маленькие,
Seq Scan по PK не нужен.

---

### Запрос 4. Три самых прибыльных фильма за неделю

```sql
SELECT m.id, m.title, SUM(t.final_price) AS total_revenue
FROM tickets t
         JOIN screenings s ON s.id = t.screening_id
         JOIN movies m ON m.id = s.movie_id
WHERE t.sold_at >= now() - interval '7 days'
GROUP BY m.id, m.title
ORDER BY total_revenue DESC
LIMIT 3;
```

**План до 10 000 строк** (`4.450 ms`): Limit → Sort → HashAggregate → два
Hash Join → Seq Scan tickets / screenings / movies.

**План до 10 000 000, до оптимизаций** (`3356.948 ms`):

```
Limit → Sort → Finalize GroupAggregate → Gather Merge → Sort
  → Partial HashAggregate
       → Hash Join (movies)
            → Parallel Hash Join
                 → Parallel Seq Scan on tickets t     -- 8 млн
                 → Parallel Hash on screenings
                      Batches: 8  Memory Usage: 3040kB
Buffers: shared hit=14674 read=68232, temp read=17832 written=17900
JIT: Total 135.707 ms
```

**План на 10 000 000 после улучшений** (`784.142 ms`):

```
Limit → Sort → Finalize GroupAggregate → Gather Merge
  → Partial HashAggregate
       → Hash Join (movies)
            → Parallel Hash Join
                 → Parallel Index Only Scan using idx_tickets_sold_at
                      Heap Fetches: 0
                 → Parallel Hash on screenings
                      Batches: 1  Memory Usage: 19776kB   -- влезло в work_mem
Buffers: shared hit=1771837   (read=0, temp нет, JIT нет)
```

**Что улучшили:** Index Only Scan вместо Seq Scan; `work_mem=64MB` убрал
разлитие Hash Join во временные файлы (~140 МБ temp); `jit=off` −136 ms;
страницы индекса в `shared_buffers`.

---

### Запрос 5. Схема зала: свободные и занятые места на сеанс

```sql
SELECT st.row_number, st.place_number, sc.name AS category,
       CASE WHEN t.id IS NOT NULL THEN 'Занято' ELSE 'Свободно' END AS status,
       sp.price
FROM seats st
         JOIN seat_categories sc ON sc.id = st.seat_category_id
         LEFT JOIN tickets t ON t.seat_id = st.id AND t.screening_id = :sid
         JOIN screening_prices sp
              ON sp.screening_id = :sid AND sp.seat_category_id = st.seat_category_id
WHERE st.hall_id = (SELECT hall_id FROM screenings WHERE id = :sid)
ORDER BY st.row_number, st.place_number;
```

**План до 10 000 строк** (`0.324 ms`): Bitmap по
`seats_hall_id_row_number_place_number_key` и
`tickets_screening_id_seat_id_key`.

**План до 10 000 000, до оптимизаций** (`3.701 ms`): те же уникальные индексы,
Bitmap Heap Scan 1000 мест, Index Scan ~20 билетов сеанса.

**План на 10 000 000 после улучшений** (`1.105 ms`):

```
Sort
  InitPlan: Index Scan using screenings_pkey
  -> Hash Join (seat_categories)
       -> Hash Left Join
            -> Bitmap Heap Scan on seats
                 -> Bitmap Index Scan on idx_seats_hall_id
            -> Index Scan using idx_tickets_screening_final_price
Buffers: shared hit=339
```

**Что улучшили:** запрос уже был быстрым (уникальные констрейнты). Добавлены
меньший `idx_seats_hall_id` и покрывающий индекс билетов. После увеличения
`shared_buffers` страницы `seats` не вытесняются тяжёлыми запросами 2 и 4
(в одном из старых прогонов из-за этого было 20 ms вместо 2.4 ms).

---

### Запрос 6. MIN/MAX цены билета на сеанс

```sql
SELECT MIN(final_price) AS min_price, MAX(final_price) AS max_price
FROM tickets
WHERE screening_id = :sid;
```

**План до 10 000 строк** (`0.053 ms`): Bitmap Index Scan по
`tickets_screening_id_seat_id_key`, ~7 билетов.

**План до 10 000 000, до оптимизаций** (`0.054 ms`): Index Scan по тому же
уникальному ключу, ~20 билетов.

**План на 10 000 000 после улучшений** (`0.027 ms`):

```
Aggregate
  -> Index Only Scan using idx_tickets_screening_final_price
       Index Cond: screening_id = :sid
       Heap Fetches: 0
```

**Что улучшили:** уже был оптимален на уникальном индексе. Покрывающий
`INCLUDE (final_price)` даёт Index Only Scan без чтения кучи; на выборке
примерно из 20 строк прирост ожидаемо небольшой.

---

### Перечень оптимизаций с пояснениями

1. **Индексы на FK и диапазон** (`sql/05_indexes.sql`):
   `idx_screenings_start_time`, `idx_screenings_movie_id`,
   `idx_screenings_hall_id`, `idx_seats_hall_id`.
   PostgreSQL не создаёт индексы на внешние ключи сам. Без них JOIN/фильтр
   по большим таблицам — Seq Scan (запросы 1 и 3 на 10M: 132 ms).

2. **Покрывающие индексы `INCLUDE`**:
   `idx_tickets_sold_at (sold_at) INCLUDE (screening_id, final_price)`,
   `idx_tickets_screening_final_price (screening_id) INCLUDE (final_price)`.
   Обычный индекс по `sold_at` при селективности недели 53% планировщик не
   берёт (Seq Scan дешевле). `INCLUDE` даёт Index Only Scan: 309 МБ индекса
   вместо 627 МБ кучи.

3. **`VACUUM ANALYZE`** после загрузки. Обновляет статистику и visibility map.
   Без битов видимости Index Only Scan не выбирается: в
   `results_10m_indexed.txt` запрос 2 всё ещё Seq Scan, 1623 ms.

4. **`shared_buffers=512MB`** (`sql/06_admin_settings.sql`, нужен рестарт).
   Тема вебинара «Страницы и кеш»: страница 8 КБ, LRU в Shared Buffers.
   Дефолт Docker 128 МБ < индекс 309 МБ → каждый прогон `shared read`.
   После 512 МБ у запросов 2 и 4 `read=0`. На выделенном сервере обычно
   ~25% RAM; контейнер делит хост, поэтому не больше.

5. **`work_mem=64MB`** — память на одну операцию Sort/Hash (каждый parallel
   worker берёт свой кусок). Дефолт 4 МБ: запрос 4, `temp read=17832`
   (~140 МБ во временные файлы). 64 МБ: `Batches: 1`, Memory 20 МБ, temp нет.
   Это `ALTER SYSTEM`, не session `PGOPTIONS`.

6. **`random_page_cost=1.1`**, **`effective_io_concurrency=200`**.
   Дефолт 4 — модель HDD. NVMe: случайное чтение почти как последовательное,
   планировщик охотнее берёт Index Scan.

7. **`jit=off`**. На запросах 2 и 4 JIT компилировал 14–79 функций и добавлял
   38–136 ms. Для этих планов не окупается.

8. **WAL:** `checkpoint_completion_target=0.9`, `max_wal_size=2GB`,
   `wal_buffers=16MB`. Растягивают сброс грязных страниц на CREATE INDEX и
   загрузке (вебинар «WAL»). На чистом SELECT почти не видны.

9. **`--shm-size=2g`**. Дефолт Docker 64 МБ. Parallel Hash при `work_mem=64MB`
   держит DSM в `/dev/shm`.

10. **RANGE-секции `tickets` по `sold_at`** (`sql/07_partition_tickets.sql`).
    Запросы 2 и 4 режутся по дате. План №2: `Subplans Removed: 2` (июнь/июль).
    Компромисс PG12: UNIQUE обязан включать ключ секции
    (`UNIQUE(screening_id, seat_id, sold_at)` слабее старого). Запросы 5 и 6
    без `sold_at` в WHERE идут `Append` по всем секциям (всё ещё < 2 ms).
    На №4 Append чуть медленнее одного Index Only Scan (929 vs 784 ms).
    Основной выигрыш дали настройки сервера, не нарезка.

11. **Что не помогло:** индекс по `sold_at` без INCLUDE; секции как ускорение
    всех шести запросов; session `work_mem` без увеличения `shared_buffers`
    (индекс всё равно не влезал в кэш).

---

## 4. ТОП-15 самых больших объектов БД

Таблицы (включая индексы) и сами индексы, по `pg_total_relation_size`,
после секций `tickets`. Полный вывод: `results/database_statistics.txt`.

| # | Объект | Тип | Всего | Своё тело |
|---|---|---|---|---|
| 1 | `tickets_2026_08` | table | 428 MB | 162 MB |
| 2 | `tickets_2026_07` | table | 427 MB | 161 MB |
| 3 | `tickets_2026_09` | table | 415 MB | 157 MB |
| 4 | `tickets_2026_10` | table | 368 MB | 139 MB |
| 5 | `screening_prices` | table | 111 MB | 60 MB |
| 6 | `tickets_2026_08_sold_at_screening_id_final_price_idx` | index | 80 MB | 80 MB |
| 7 | `tickets_2026_07_sold_at_screening_id_final_price_idx` | index | 80 MB | 80 MB |
| 8 | `tickets_2026_09_sold_at_screening_id_final_price_idx` | index | 77 MB | 77 MB |
| 9 | `tickets_2026_10_sold_at_screening_id_final_price_idx` | index | 69 MB | 69 MB |
| 10 | `tickets_2026_08_screening_id_final_price_idx` | index | 62 MB | 62 MB |
| 11 | `tickets_2026_08_pkey` | index | 62 MB | 62 MB |
| 12 | `tickets_2026_08_screening_id_seat_id_sold_at_key` | index | 62 MB | 62 MB |
| 13 | `tickets_2026_07_screening_id_final_price_idx` | index | 62 MB | 62 MB |
| 14 | `tickets_2026_07_screening_id_seat_id_sold_at_key` | index | 62 MB | 62 MB |
| 15 | `tickets_2026_07_pkey` | index | 62 MB | 62 MB |

Родитель `tickets` виртуальный (0 байт): данные в месячных секциях.
Август 2.07 млн строк, июль 2.06, сентябрь 2.00, октябрь 1.77, июнь 0.10,
default пустая.

---

## 5. Использование индексов

После `pg_stat_reset()` и 10 прогонов `sql/04_queries.sql`.

**ТОП-5 самых часто используемых**

| # | Индекс | Сканов | Размер |
|---|---|---|---|
| 1 | `idx_screenings_movie_id` | 60 | 8808 kB |
| 2 | `movies_pkey` | 60 | 40 kB |
| 3 | `seat_categories_pkey` | 60 | 16 kB |
| 4 | `screenings_pkey` | 60 | 8792 kB |
| 5 | `tickets_2026_09_sold_at_screening_id_final_price_idx` | 50 | 77 MB |

**ТОП-5 самых редко используемых**

| # | Индекс | Сканов | Размер |
|---|---|---|---|
| 1 | `tickets_2026_08_screening_id_seat_id_sold_at_key` | 0 | 62 MB |
| 2 | `tickets_2026_08_pkey` | 0 | 62 MB |
| 3 | `tickets_2026_07_pkey` | 0 | 62 MB |
| 4 | `tickets_2026_07_screening_id_seat_id_sold_at_key` | 0 | 62 MB |
| 5 | `tickets_2026_09_pkey` | 0 | 60 MB |

Нули — PK и UNIQUE секций. На этой нагрузке ищем по `sold_at` и
`screening_id`, не по `id` билета. Дропать нельзя: целостность
(`UNIQUE(screening_id, seat_id, sold_at)` заменяет старый
`UNIQUE(screening_id, seat_id)`).

---

## Как воспроизвести

```bash
docker run -d --name hw9-pg --shm-size=2g -p 55432:5432 \
    -e POSTGRES_PASSWORD=secret -e POSTGRES_DB=cinema \
    postgres:12.4-alpine

# 10k
docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/01_schema.sql
docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/02_fill_10k.sql
docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/04_queries.sql \
    > results/results_10k.txt

# 10M (~10–15 мин)
docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/01_schema.sql
docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/03_fill_10m.sql
docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/04_queries.sql \
    > results/results_10m.txt

docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/05_indexes.sql
docker exec hw9-pg psql -U postgres -d cinema -c "VACUUM ANALYZE;"
docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/06_admin_settings.sql
docker restart hw9-pg
docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/04_queries.sql \
    > results/results_10m_admin.txt

docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/07_partition_tickets.sql
docker exec -i hw9-pg psql -U postgres -d cinema -f /tmp/database_statistics.sql \
    > results/database_statistics.txt
```
