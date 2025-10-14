# PHP_2025

ДЗ по уроку #7 Проектирование базы даннных для кинотеатра

erDiagram
	direction TB
	movies {
		SEQUENCE id PK ""  
		NVARCHAR TITLE  ""  
		TEXT DESCRIPTION  ""  
		DATE GLOBAL_RELEASE_DATE  ""  
		DATE GLOBAL_END_DATE  ""  
		INTEGER DURATION_MINUTES  ""  
		NUMERIC BASE_COST  ""  
	}

	customers {
		SEQUENCE id PK ""  
		NVARCHAR FIRST_NAME  ""  
		NVARCHAR SURNAME  ""  
		VARCHAR MAIL  ""  
		VARCHAR PHONE  ""  
	}

	places {
		SEQUENCE id PK ""  
		INTEGER HALS_ID  ""  
		INTEGER ROW_ID  ""  
		INTEGER POSITION  ""  
		INTEGER COST_MODIFIER  ""  
	}

	session {
		SEQUENCE id PK ""  
		INTEGER FILM_ID  ""  
		INTEGER HALS_ID  ""  
		TIMESTRAMP START_SESSION  ""  
		TIMESTRAMP END_SESSION  ""  
		INTEGER COST_MODIFIER  ""  
	}

	hals {
		SEQUENCE id PK ""  
		NVARCHAR HALS_NAME  ""  
		INTEGER CAPACITY  ""  
		INTEGER AMOUNT_ROW  ""  
		INTEGER COST_MODIFIER  ""  
	}

	tickets {
		SEQUENCE id PK ""  
		INTEGER CLIENT_ID  ""  
		INTEGER SESSION_ID  ""  
		NUMERIC TICKET_COST  ""  
	}

	places}|--||hals:"  "
	session}|--|{movies:"  "
	session}|--|{hals:"  "
	tickets}|--||customers:"  "
	tickets}|--||session:"  "



