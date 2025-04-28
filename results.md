## Результаты запросов

| Запрос                                           | Cost для 10k   | Execution Time для 10k | Cost для 1m        | Execution Time для 1m | Cost для 1m  c индексом | Execution Time для 1m с индексом |
| ------------------------------------------------ | -------------- | ---------------------- | ------------------ | --------------------- | ----------------------- | -------------------------------- |
| 1. Сумма проданных билетов за сеанс              | 60.50..60.51   | 0.238 ms               | 13599.23..13599.24 | 33.112 ms             | 196.86..196.87          | 0.386 ms                         |
| 2. Количество сеансов за последнюю неделю        | 294.00..294.01 | 2.545 ms               | 587.00..587.00     | 11.534 ms             | 587.00..587.01          | 4.255 ms                         |
| 3 Фильмы которые показывают сегодня              | 270.41..270.66 | 1.033 ms               | 540.32..540.82     | 4.206 ms              | 540.32..540.82          | 2.401 ms                         |
| 4. Подсчёт дохода от проданных билетов за неделю | 462.36..462.37 | 4.237 ms               | 15483.31..15483.32 | 193.190 ms            | 15483.44..15483.45      | 126.448 ms                       |
| 5. Поиск 3 самых прибыльных фильмов за неделю    | 494.33..494.34 | 7.105 ms               | 17714.76..17714.76 | 417.665 ms            | 17715.10..17715.11      | 376.448 ms                       |
| 6 Свободные и занятые места на конкретный сеанс  | 101.20..101.53 | 0.553 ms               | 14321.09..14322.00 | 31.074 ms             | 504.27..505.18          | 2.901 ms                         |



## Зтоп 15 самых больших объектов (таблиц, индексов) в базе данных

| schema_name | object_name                | object_type | total_size | table_size | index_size |
|:------------|:----------------------------|:------------|-----------:|-----------:|-----------:|
| public      | tickets                     | Table       | 123 MB     | 58 MB      | 66 MB      |
| public      | idx_tickets_seat_session     | Index       | 30 MB      | 30 MB      | 0 bytes    |
| public      | tickets_pk                   | Index       | 29 MB      | 29 MB      | 0 bytes    |
| public      | idx_tickets_session_id        | Index       | 6992 kB    | 6992 kB    | 0 bytes    |
| public      | seats                        | Table       | 4096 kB    | 2048 kB    | 2048 kB    |
| public      | sessions                     | Table       | 3048 kB    | 1528 kB    | 1520 kB    |
| public      | seats_pk                     | Index       | 768 kB     | 768 kB     | 0 bytes    |
| public      | idx_seats_id                  | Index       | 768 kB     | 768 kB     | 0 bytes    |
| public      | idx_sessions_session_start    | Index       | 456 kB     | 456 kB     | 0 bytes    |
| public      | sessions_pk                  | Index       | 456 kB     | 456 kB     | 0 bytes    |
| public      | idx_sessions_id               | Index       | 456 kB     | 456 kB     | 0 bytes    |
| public      | idx_seats_row_number          | Index       | 264 kB     | 264 kB     | 0 bytes    |
| public      | idx_seats_hall_id              | Index       | 248 kB     | 248 kB     | 0 bytes    |
| public      | idx_sessions_film_id           | Index       | 152 kB     | 152 kB     | 0 bytes    |
| public      | films                         | Table       | 80 kB      | 64 kB      | 16 kB      |


## топ 5 самых часто используемых индексов

|index_name |table_name |index_usage_count|index_size|
|-----------|-----------|-----------------|----------|
|sessions   |sessions   |          1012014|456 kB    |
|seats      |seats      |          1012010|768 kB    |
|halls      |halls      |            55637|16 kB     |
|seats_class|seats_class|            35637|16 kB     |
|films      |films      |            20010|16 kB     |


## топ 5 самых редко используемых индексов

|index_name|table_name|index_usage_count|index_size|
|----------|----------|-----------------|----------|
|sessions  |sessions  |                0|152 kB    |
|seats     |seats     |                0|264 kB    |