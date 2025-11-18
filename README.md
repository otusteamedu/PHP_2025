# PHP_2025 Проектирование БД

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

# Описание 

В рамках задачи была спроектирована БД для системы управления кинотеатром


# Схема

```mermaid 

---
config:
  layout: dagre
  look: neutral
---
erDiagram
	direction TB
	room {
		integer room_id PK ""  
		integer theatre_id FK ""  
		varchar(100) name  ""  
	}

	user {
		integer user_id PK ""  
		varchar(255) name  ""  
		varchar(255) email UK ""  
		varchar(25) phone UK ""  
		datetime registration_date  ""  
	}

	session {
		integer session_id PK ""  
		integer room_id FK ""  
		datetime start_time  ""  
		datetime end_time  ""  
		integer movie_id FK ""  
		decimal base_price 
	}

	theatre {
		integer theatre_id PK ""  
		varchar(200) name  ""  
		text location  ""  
	}

	order_status {
		integer order_status_id PK ""  
		varchar(100) status_name  ""  
	}

	seat_type {
		integer seat_type_id PK ""  
		varchar(100) name  ""  
		decimal price_modifier 
	}

	movie {
		integer movie_id PK ""  
		varchar(255) movie_name  ""  
		integer duration  ""  
		text movie_description  ""  
		date release_date  ""  
		float rating  ""  
	}

	order {
		integer order_id PK ""  
		integer user_id FK ""  
		datetime created_at  ""  
		integer order_status_id FK ""  
		decimal order_total_price 
	}

	booking {
		integer booking_id PK ""  
		integer order_id FK ""  
		integer session_id FK ""  
		integer seat_id FK ""  
		decimal booking_price 
	}

	seat {
		integer seat_id PK ""  
		integer room_id FK ""  
		integer row_number  ""  
		integer seat_number  ""  
		integer seat_type_id FK ""  
	}

	user||--|{order:"  "
	order||--|{booking:" "
	order}|--||order_status:"  "
	session}o--||room:"  "
	session||--o{booking:"  "
	booking}o--||seat:" "
	seat}|--||seat_type:"  "
	seat}|--||room:"  "
	theatre||--|{room:"  "
	movie||--|{session:"  "

```