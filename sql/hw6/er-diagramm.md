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
    
    hall_type {
        smallint id PK
        varchar(50) type_name
        numeric price_modifier
    }

    hall {
        integer id PK
        varchar(255) name
        smallint hall_type_id FK
    }
    
    seat_type {
        smallint id PK
        varchar(50) type_name
        numeric price_modifier
    }
    
    seat {
        integer id PK
        integer row_number
        integer seat_number
        smallint seat_type_id FK
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
    
    session_period {
        smallint id PK
        varchar(50) period_name
        numeric price_modifier
    }

    session {
        bigint id PK
        timestamp(0) start_time
        timestamp(0) end_time
        bigint movie_id FK
        integer hall_id FK
        smallint session_period_id FK
    }
    
    session_price {
        bigint id PK
        numeric price
        bigint session_id FK
        integer seat_id FK
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
    session ||--|{ session_price : has
    seat ||--|| session_price : matches
    hall_type ||--|{ hall : belongs
    seat_type ||--|{ seat : belongs
    session_period ||--|{ session : matches
