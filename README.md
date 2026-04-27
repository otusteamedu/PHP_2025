# Схема данных кинотеатр

Описание/Пошаговая инструкция выполнения домашнего задания:

Подготовить список из 6 основных запросов к БД, разработанной на предыдущих занятиях

1. Выбор всех фильмов на сегодня

2. Подсчёт проданных билетов за неделю

3. Формирование афиши (фильмы, которые показывают сегодня)

4. Поиск 3 самых прибыльных фильмов за неделю

5. Сформировать схему зала и показать на ней свободные и занятые места на конкретный сеанс

6. Вывести диапазон минимальной и максимальной цены за билет на конкретный сеанс

Целесообразно выбрать 3 "простых" (задействована 1 таблица), 3 "сложных" (агрегатные функции, связи таблиц).

Далее нужно заполнить таблицы, увеличив общее количество строк текстовых данных до 10000.

Затем проведите анализ производительности запросов к БД, сохранить планы выполнения.


Заполните таблицы, увеличив общее количество строк текстовых данных до 10000000.

Затем проведите анализ производительности запросов к БД, сохранить планы выполнения.


На основе анализа запросов и планов предложить оптимизации (индексы, структура, параметры и др.

Добавьте индексы и сравните результат, приложив планы выполнения.
---

Схему БД можно посмотреть на сайте [Mermaid](https://mermaid.live/edit#pako:eNqdUkFuwjAQ_Iq154CcBCXYV-DQA6KicKkiVVZsgtXETh1bKg38vQ4hqCgSh-5pZzQ7ux65hVxzARSEWUpWGFZlCvla7N92m_Vqi87nyUS3aLNdekBRXbJcNL2m5zrB-Sb4eNmt1l6Va2WZVDfd63az3C92D1aDUqq8dHxwvG9te9xVY41UBZJ8RClWiREpKibLnr38PfO5I2dWIG24T8F3I2ljmXXNg-vwqH9ceig1s6g2MhfjQ_tg_rhKZdGXY8pKe3rmAQEURnKg1jgRQCWMT8JDuHplYI_CHwHUt5yZzwwy1c3UTL1rXQ1jRrviCPTAysYjV3fB3D7GXSKUz2mhnbJAQ3K1ANrCt0fzdBqlcRITQnCE01kAJ6BRNMUkImFCYhzPcRxGlwB-rkvDaRjGKU7JLMRRMsM4ufwCMejARA), вставив код для создания схемы из файла cinema.mmd.


![Схема БД](img/cinems-db-schema.svg)
# Описание кинотеатра

Клиент может купить несколько билетов на сеанс в кинотеатре. 

Места в залах могут иметь разную цену.
---
# Анализ производительности запросов к БД

## Количество строк текстовых данных до 10000

| Запрос     | Затраты на получение первой строки | Затраты на получение всех строк |
|------------|------------------------------------|---------------------------------|
| Простой №1 | 8.31                               | 8.32                            |
| Простой №2 | 0.29                               | 4.39                            |
| Простой №3 | 0.29                               | 8.30                            |
| Сложный №1 | 906.22                             | 907.26                          |
| Сложный №2 | 447.79                             | 447.8                           |
| Сложный №3 | 581.1                              | 583.54                          |


### Простой запрос №1
![10-thousand-records-simple-request-1.png](img/explain/10-thousand-records-simple-request-1.png)
### Простой запрос №2
![10-thousand-records-simple-request-2.png](img/explain/10-thousand-records-simple-request-2.png)
### Простой запрос №3
![10-thousand-records-simple-request-3.png](img/explain/10-thousand-records-simple-request-3.png)

### Сложный запрос №1
![10-thousand-records-complex-request-1.png](img/explain/10-thousand-records-complex-request-1.png)
### Сложный запрос №2
![10-thousand-records-complex-request-2.png](img/explain/10-thousand-records-complex-request-2.png)
### Сложный запрос №3
![10-thousand-records-complex-request-3.png](img/explain/10-thousand-records-complex-request-3.png)

## Количество строк текстовых данных до 10.000.000
| Запрос     | Затраты на получение первой строки | Затраты на получение всех строк |
|------------|------------------------------------|---------------------------------|
| Простой №1 | 8.46                               | 8.47                            |
| Простой №2 | 0.43                               | 4.45                            |
| Простой №3 | 0.43                               | 8.45                            |
| Сложный №1 | 264262.06                          | 264286.36                       |
| Сложный №2 | 241532.21                          | 241532.21                       |
| Сложный №3 | 1145.73                            | 1148.07                         |



### Простой запрос №1
![10-million-records-simple-request-1.png](img/explain/10-million-records-simple-request-1.png)
### Простой запрос №2
![10-million-records-simple-request-2.png](img/explain/10-million-records-simple-request-2.png)
### Простой запрос №3
![10-million-records-simple-request-3.png](img/explain/10-million-records-simple-request-3.png)

### Сложный запрос №1
![10-million-records-complex-request-1.png](img/explain/10-million-records-complex-request-1.png)
### Сложный запрос №2
![10-million-records-complex-request-2.png](img/explain/10-million-records-complex-request-2.png)
### Сложный запрос №3
![10-million-records-complex-request-3.png](img/explain/10-million-records-complex-request-3.png)



## Идеи для оптимизации

Для простых запросов стоимость не большая, предлогаю их не оптимизировать.

### Сложный запрос №1
Для таблицы cinema.ticket необходимо включить поле session_id в индекс.

### Сложный запрос №2
Для таблицы cinema.orders необходимо включить поле created_at и id в индекс.

### Сложный запрос №3
Для таблицы cinema.place необходимо включить поле hall_id в индекс.
