# PHP_2025

ДЗ по уроку #7 Проектирование базы даннных для кинотеатра

В рамках ДЗ выполнена DDL базы данных, учитывающая разные залы, разные фильмы



```mermaid
erDiagram
    direction TB
    movies {
        BIGINT id PK "SEQUENCE"
        NVARCHAR TITLE 
        TEXT DESCRIPTION
        DATE GLOBAL_RELEASE_DATE 
        DATE GLOBAL_END_DATE  ""  
        INTEGER DURATION_MINUTES 
        NUMERIC BASE_COST
        INTEGER AGE_RESTRICTION  ""
    }

    customers {
        SEQUENCE id PK ""  
        NVARCHAR FIRST_NAME 
        NVARCHAR SURNAME 
        VARCHAR MAIL  ""  
        VARCHAR PHONE  ""  
    }

    places {
        SEQUENCE id PK ""  
        INTEGER HALS_ID  "REFERENCES hals(id)"  
        INTEGER ROW_ID  ""  
        INTEGER POSITION  ""  
        INTEGER COST_MODIFIER  ""  
    }

    movie_sessions {
        BIGINT id PK "SEQUENCE"
        INTEGER MOVIES_ID  "REFERENCES movies(id)"  
        INTEGER HALS_ID  "REFERENCES hals(id)"  
        TIMESTAMP START_SESSION   
        TIMESTAMP END_SESSION  
        INTEGER COST_MODIFIER  ""  
    }

    hals {
        BIGINT id PK "SEQUENCE"
        NVARCHAR HALS_NAME 
        INTEGER CAPACITY  
        INTEGER AMOUNT_ROW   
        INTEGER COST_MODIFIER  
    }

    tickets {
        BIGINT id PK "SEQUENCE"
        INTEGER CLIENT_ID "REFERENCES customers(id)"  
        INTEGER SESSION_ID "REFERENCES movie_sessions(id)"  
        NUMERIC TICKET_COST ""  
        TIMESTAMP TIME_OF_BUY "" 
        BIGINT place_id FK "REFERENCES places(id)"


    }

    places}|--||hals:"  "
    movie_sessions}|--|{movies:"  "
    movie_sessions}|--|{hals:"  "
    tickets}|--||customers:"  "
    tickets}|--||movie_sessions:"  "


