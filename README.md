## Система управления кинотеатром

Цена = базовая цена сеанса + модификатор по типу места. Билет может быть в статусах: reserved, sold, cancelled. Схема зала — сетка ряд×место, у места есть тип (например, VIP).

```mermaid
erDiagram

    HALL {
        bigint id PK
        varchar name
        integer seat_rows
        integer seat_columns
        boolean is_active
    }

    SEAT_TYPE {
        bigint id PK
        varchar code
        varchar name
        numeric price_modifier
    }

    SEAT {
        bigint id PK
        bigint hall_id FK
        integer row_number
        integer seat_number
        bigint seat_type_id FK
    }

    MOVIE {
        bigint id PK
        varchar title
        integer duration_minutes
        varchar rating
        date release_date
    }

    TICKET_STATUS {
        varchar code PK
        varchar name
    }

    SHOWTIME {
        bigint id PK
        bigint hall_id FK
        bigint movie_id FK
        timestamptz starts_at
        timestamptz ends_at
        numeric base_price
    }

    CUSTOMER {
        bigint id PK
        citext email
        varchar phone
        varchar full_name
        timestamptz created_at
    }

    TICKET {
        bigint id PK
        bigint showtime_id FK
        bigint seat_id FK
        varchar status FK
        numeric price_paid
        timestamptz reserved_at
        timestamptz sold_at
        timestamptz cancelled_at
        bigint customer_id FK
    }


    %% Relationships
    HALL ||--o{ SEAT : "contains"
    HALL ||--o{ SHOWTIME : "hosts"
    SEAT_TYPE ||--o{ SEAT : "defines type"
    MOVIE ||--o{ SHOWTIME : "is shown in"
    TICKET_STATUS ||--o{ TICKET : "status"
    SHOWTIME ||--o{ TICKET : "has"
    SEAT ||--o{ TICKET : "booked"
    TICKET ||--o{ CUSTOMER : "is owned by"
```
