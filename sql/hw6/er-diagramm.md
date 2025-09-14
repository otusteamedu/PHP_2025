```mermaid
---
title: Логическая модель БД кинотеатра (ER-диаграмма)
---
erDiagram
    client {
        bigint id PK
        varchar(255) email
        varchar(20) phone
    }
    
    order {
        bigint id PK
        numeric total_price
        timestamp(0) created_at
        bigint client_id FK
    }

    hall {
        integer id PK
        varchar(255) name
        varchar(50) type
    }
    
    seat {
        integer id PK
        integer row_number
        integer seat_number
        varchar(50) type
        integer hall_id FK
    }

    movie {
        bigint id PK
        varchar(255) name
        text description
        integer duration
        real rating
        date release_date
    }

    session {
        bigint id PK
        timestamp(0) start_time
        timestamp(0) end_time
        bigint movie_id FK
        integer hall_id FK
    }

    ticket {
        bigint id PK
        numeric price
        bigint order_id FK
        bigint session_id FK
        integer seat_id FK
    }

    client ||--|{ order : has
    order ||--|{ ticket : contains
    hall ||--|{ seat : contains
    hall ||--|{ session : organizes
    movie ||--|{ session : shown
    session ||--|{ ticket : belongs
    seat ||--|{ ticket : matches
