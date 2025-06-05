# PHP_2025

## Описание выполненного ДЗ №4
В рамках домашнего задания спроектированна схема данных для системы управления кинотеатром.
Написан запрос по определению самого прибыльного фильма.

### Логическая схема:
```mermaid
---
config:
  theme: neutral
---
erDiagram
    screenings }o--|| movies : ""
    screenings }o--|| halls : ""
    seats }o--|| seat_types : ""
    seats }o--|| halls : ""
    tickets }o--|| customers : ""
    tickets }o--|| screenings : ""
    tickets }o--|| seats : ""
    pricing_rules }o--|| screenings : ""
    pricing_rules }o--|| seat_types : ""

    customers {
        UUID ID PK
        VARCHAR(100) NAME
        VARCHAR(120) EMAIL
        VARCHAR(50) PHONE
        DATE REGISTER_DATA
    }

    halls {
        UUID ID PK
        VARCHAR(100) NAME
        INTEGER CAPACITY
        TEXT DESCRIPTION
    }

    movies {
        UUID ID PK
        VARCHAR(200) TITLE
        SMALLINT DURATION
        VARCHAR(50) GENRE
        DATE RELEASE_DATE
    }

    screenings {
        UUID ID PK
        UUID MOVIE_ID
        UUID HALL_ID
        TIMESTAMP SCREENING_START
        TIMESTAMP SCREENING_END
        SMALLINT BASE_PRICE
    }

    seat_types {
        UUID ID PK
        VARCHAR(50) TITLE
    }

    seats {
        UUID ID PK
        SMALLINT ROW_NUMBER
        SMALLINT SEAT_NUMBER
        UUID SEAT_TYPE_ID
        UUID HALL_ID
    }

    tickets {
        UUID ID PK
        UUID CUSTOMER_ID
        UUID SCREENING_ID
        UUID SEAT_ID
        SMALLINT PRICE
        TIMESTAMP PURCHASE_DATE
    }

    pricing_rules {
        UUID ID PK
        UUID SCREENING_ID
        UUID SEAT_TYPE_ID
        NUMERIC(42) MODIFIER
    }
```