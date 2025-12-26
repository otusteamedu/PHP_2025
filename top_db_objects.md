# Топ-15 самых больших объектов БД

| №   | Объект                            | Тип   | Размер   | Размер (байт) |
| --- | --------------------------------- | ----- | -------- | ------------- |
| 1   | ticket                            | table | 551 MB   | 578 142 208   |
| 2   | idx_ticket_session_seat           | index | 206 MB   | 215 613 440   |
| 3   | ticket_pkey                       | index | 206 MB   | 215 597 056   |
| 4   | ticket_session_id_seat_id_key     | index | 206 MB   | 215 506 944   |
| 5   | idx_ticket_paid_purchased         | index | 48 MB    | 49 971 200    |
| 6   | session                           | table | 7 064 kB | 7 233 536     |
| 7   | session_pkey                      | index | 2 640 kB | 2 703 360     |
| 8   | idx_session_start_movie           | index | 1 192 kB | 1 220 608     |
| 9   | seat                              | table | 616 kB   | 630 784       |
| 10  | seat_hall_id_nrow_seat_number_key | index | 496 kB   | 507 904       |
| 11  | idx_seat_hall_row                 | index | 384 kB   | 393 216       |
| 12  | seat_pkey                         | index | 280 kB   | 286 720       |
| 13  | idx_seat_hall_category            | index | 96 kB    | 98 304        |
| 14  | idx_seat_hall                     | index | 96 kB    | 98 304        |
| 15  | attribute_values                  | table | 96 kB    | 98 304        |

---

## Топ-5 самых часто используемых индексов

| №   | Схема  | Таблица   | Индекс                    | Использований |
| --- | ------ | --------- | ------------------------- | ------------- |
| 1   | public | movie     | movie_pkey                | 554           |
| 2   | public | hall      | hall_pkey                 | 28            |
| 3   | public | attribute | attribute_pkey            | 24            |
| 4   | public | ticket    | idx_ticket_paid_purchased | 18            |
| 5   | public | session   | session_pkey              | 14            |

---

## Топ-5 самых редко используемых индексов

| №   | Схема  | Таблица        | Индекс                  | Использований |
| --- | ------ | -------------- | ----------------------- | ------------- |
| 1   | public | seat           | idx_seat_hall           | 2             |
| 2   | public | seat           | idx_seat_hall_category  | 2             |
| 3   | public | attribute_type | idx_attr_type_data_type | 3             |
| 4   | public | seat           | seat_pkey               | 4             |
| 5   | public | attribute      | idx_attribute_type      | 6             |

---
