# Схема данных для системы управления кинотеатром

Кинотеатр имеет несколько залов, в каждом зале идет несколько разных сеансов, клиенты могут купить билеты на сеансы


## Логическая модель
```mermaid
classDiagram
direction BT
class customers {
   varchar(50) first_name
   varchar(50) last_name
   varchar(100) email
   varchar(20) phone
   timestamp registration_date
   integer customer_id
}
class hall_layouts {
   integer hall_id
   varchar(100) name
   text description
   integer total_seats
   integer total_rows
   jsonb layout_schema
   timestamp created_at
   timestamp updated_at
   integer layout_id
}
class halls {
   varchar(50) name
   varchar(20) hall_type
   integer capacity
   timestamp created_at
   integer hall_id
}
class movies {
   varchar(100) title
   text description
   integer duration_minutes
   varchar(10) rating
   varchar(50) genre
   date release_date
   timestamp created_at
   integer movie_id
}
class prices {
   integer screening_id
   varchar(20) seat_type
   numeric(10,2) price
   timestamp created_at
   integer price_id
}
class screenings {
   integer movie_id
   integer hall_id
   timestamp start_time
   timestamp end_time
   varchar(20) format
   numeric(10,2) base_price
   timestamp created_at
   integer screening_id
}
class seats {
   integer hall_id
   integer layout_id
   varchar(10) row_id
   integer seat_number
   varchar(20) seat_type
   varchar(50) zone
   boolean is_active
   timestamp created_at
   timestamp updated_at
   integer seat_id
}
class staff {
   varchar(50) first_name
   varchar(50) last_name
   varchar(50) position
   varchar(100) email
   varchar(20) phone
   date hire_date
   integer staff_id
}
class tickets {
   integer screening_id
   integer seat_id
   integer customer_id
   numeric(10,2) price
   timestamp purchase_time
   varchar(20) status
   integer ticket_id
}

hall_layouts  -->  halls : hall_id
prices  -->  screenings : screening_id
screenings  -->  halls : hall_id
screenings  -->  movies : movie_id
seats  -->  hall_layouts : layout_id
seats  -->  halls : hall_id
tickets  -->  customers : customer_id
tickets  -->  screenings : screening_id
```

## Пример JSON для схемы зала в таблице hall_layouts

```json
{
    "version": "1.0",
    "rows": [
        {
            "row_id": "A",
            "row_name": "Первый ряд",
            "seats": [
                {"seat_number": 1, "seat_type": "vip", "zone": "center"},
                {"seat_number": 2, "seat_type": "vip", "zone": "center"}
            ]
        },
        {
            "row_id": "B",
            "row_name": "Второй ряд",
            "seats": [
                {"seat_number": 1, "seat_type": "vip", "zone": "left"},
                {"seat_number": 2, "seat_type": "vip", "zone": "center"},
                {"seat_number": 3, "seat_type": "vip", "zone": "center"},
                {"seat_number": 4, "seat_type": "vip", "zone": "right"}
            ]
        },
        {
            "row_id": "C",
            "row_name": "Третий ряд",
            "seats": [
                {"seat_number": 1, "seat_type": "standard", "zone": "left"},
                {"seat_number": 2, "seat_type": "standard", "zone": "left"},
                {"seat_number": 3, "seat_type": "standard", "zone": "center"},
                {"seat_number": 4, "seat_type": "standard", "zone": "center"},
                {"seat_number": 5, "seat_type": "standard", "zone": "right"},
                {"seat_number": 6, "seat_type": "standard", "zone": "right"}
            ]
        }
    ],
    "zones": {
        "center": {"color": "#FF0000", "price_multiplier": 1.3},
        "left": {"color": "#00FF00", "price_multiplier": 1.0},
        "right": {"color": "#0000FF", "price_multiplier": 1.0}
    }
}
```
## Запрос для нахождения самого прибыльного фильма

```sql
WITH movie_revenue AS (
    SELECT 
        m.movie_id,
        m.title,
        SUM(t.price) AS total_revenue,
        COUNT(t.ticket_id) AS tickets_sold
    FROM 
        movies m
    JOIN 
        screenings s ON m.movie_id = s.movie_id
    JOIN 
        tickets t ON s.screening_id = t.screening_id
    WHERE 
        t.status = 'active'
    GROUP BY 
        m.movie_id, m.title
)
SELECT 
    movie_id,
    title,
    total_revenue,
    tickets_sold
FROM 
    movie_revenue
ORDER BY 
    total_revenue DESC
LIMIT 1;
```


