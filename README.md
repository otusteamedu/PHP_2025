# HW9

**Файлы:**  
[cinema_ddl.sql](cinema_ddl.sql) - скрипты для создания таблиц из прошлого ДЗ  
[cinema_indexes.sql](cinema_indexes.sql) - созданные индексы для оптимизации с пояснениями  
[cinema_queries.sql](cinema_queries.sql) - 6 запросов  
[cinema_stat.xlsx](cinema_stat.xlsx) - таблица со статистикой  
[fill_10k.sql](fill_10k.sql) - скрипты на вставку 10000 строк  
[fill_10m.sql](fill_10m.sql) - скрипты на вставку 10000000 строк
---

**Краткая сводка по [таблице](cinema_stat.xlsx):**

| # | Запрос                                | 10К      | 10М       | 10М с индексами  | Ускорение |
|---|---------------------------------------|----------|-----------|------------------|-----------|
| 1 | Выбор всех фильмов на сегодня         | 0.370 ms | 35.512 ms | 3.030 ms         | 11.7x     |
| 2 | Подсчёт проданных билетов за неделю   | 0.571 ms | 54.996 ms | 13.188 ms        | 4.2x      |
| 3 | Формирование афиши на сегодня         | 0.435 ms | 5.551 ms  | 3.652 ms         | 1.5x      |
| 4 | Три самых прибыльных фильма за неделю | 0.767 ms | 86.011 ms | 34.368 ms        | 2.5x      |
| 5 | Схема зала для конкретного сеанса     | 0.508 ms | 24.790 ms | 0.482 ms         | 51.4x     |
| 6 | Диапазон цен на конкретный сеанс      | 0.505 ms | 15.628 ms | 0.280 ms         | 55.8x     |

---
**Список самых больших по размеру объектов БД (15 значений):**

| type   | name                              | size    |
|--------|-----------------------------------|---------|
| table  | order                             | 778 MB  |
| table  | customer                          | 505 MB  |
| index  | customer_email_uindex             | 210 MB  |
| index  | order_pk                          | 146 MB  |
| index  | order_showtime_id_seat_id_uindex  | 143 MB  |
| table  | showtime                          | 70 MB   |
| index  | customer_pk                       | 54 MB   |
| index  | idx_order_showtime_id             | 53 MB   |
| index  | idx_order_seat_id                 | 46 MB   |
| index  | showtime_hall_id_time_uindex      | 15 MB   |
| table  | seat                              | 13 MB   |
| index  | idx_showtime_time_movie_id        | 12 MB   |
| index  | idx_showtime_time                 | 8792 kB |
| index  | showtime_pk                       | 8792 kB |
| index  | seat_pk                           | 3312 kB |

---
**Список часто используемых индексов (5 значений):**

| table     | index                            | scans    | tuples_read | tuples_fetched |
|-----------|----------------------------------|----------|-------------|----------------|
| seat      | seat_pk                          | 13600016 | 13600016    | 13600000       |
| seat_type | seat_type_pk                     | 6950057  | 6950057     | 6950057        |
| order     | order_showtime_id_seat_id_uindex | 6852707  | 895507      | 671619         |
| showtime  | showtime_pk                      | 6800087  | 6800087     | 6800015        |
| customer  | customer_pk                      | 6800000  | 6800000     | 6800000        |

**Список редко используемых индексов (5 значений):**

| table    | index                      | scans | tuples_read | tuples_fetched |
|----------|----------------------------|-------|-------------|----------------|
| customer | customer_email_uindex      | 0     | 0           | 0              |
| order    | order_pk                   | 0     | 0           | 0              |
| seat     | idx_seat_seat_type_id      | 0     | 0           | 0              |
| showtime | idx_showtime_time_movie_id | 6     | 2472        | 0              |
| order    | idx_order_seat_id          | 10    | 10          | 0              |
