CREATE SCHEMA IF NOT EXISTS "public";

CREATE  TABLE "public".customers ( 
	id                   serial  NOT NULL  ,
	name                 varchar(100)  NOT NULL  ,
	email                varchar(100)    ,
	phone                varchar(50)    ,
	registration_date    date  NOT NULL  ,
	CONSTRAINT pk_customers PRIMARY KEY ( id )
 );

CREATE  TABLE "public".halls ( 
	id                   serial  NOT NULL  ,
	name                 varchar(100)  NOT NULL  ,
	capacity             integer  NOT NULL  ,
	description          text  NOT NULL  ,
	CONSTRAINT pk_halls PRIMARY KEY ( id )
 );

CREATE  TABLE "public".movies ( 
	id                   serial  NOT NULL  ,
	title                varchar(200)  NOT NULL  ,
	duration             smallint  NOT NULL  ,
	genre                varchar(50)  NOT NULL  ,
	release_year         smallint  NOT NULL  ,
	CONSTRAINT pk_movies PRIMARY KEY ( id )
 );

CREATE  TABLE "public".screenings ( 
	id                   serial  NOT NULL  ,
	movie_id             integer  NOT NULL  ,
	hall_id              integer  NOT NULL  ,
	start_time           timestamp  NOT NULL  ,
	end_time             timestamp  NOT NULL  ,
	base_price           smallint  NOT NULL  ,
	CONSTRAINT pk_screening PRIMARY KEY ( id ),
	CONSTRAINT fk_screening_movies FOREIGN KEY ( movie_id ) REFERENCES "public".movies( id )   ,
	CONSTRAINT fk_screening_halls FOREIGN KEY ( hall_id ) REFERENCES "public".halls( id )   
 );

CREATE  TABLE "public".seat_types ( 
	id                   serial  NOT NULL  ,
	title                varchar(50)  NOT NULL  ,
	CONSTRAINT pk_seat_types PRIMARY KEY ( id )
 );

CREATE  TABLE "public".seats ( 
	id                   serial  NOT NULL  ,
	row_number           smallint  NOT NULL  ,
	seat_number          smallint  NOT NULL  ,
	seat_type_id         integer  NOT NULL  ,
	hall_id              integer  NOT NULL  ,
	CONSTRAINT pk_seats PRIMARY KEY ( id ),
	CONSTRAINT fk_seats_seat_types FOREIGN KEY ( seat_type_id ) REFERENCES "public".seat_types( id )   ,
	CONSTRAINT fk_seats_halls FOREIGN KEY ( hall_id ) REFERENCES "public".halls( id )   
 );

CREATE  TABLE "public".tickets ( 
	id                   serial  NOT NULL  ,
	customer_id          integer  NOT NULL  ,
	screening_id         integer  NOT NULL  ,
	seat_id              integer  NOT NULL  ,
	price                smallint  NOT NULL  ,
	purchase_date        timestamp  NOT NULL  ,
	CONSTRAINT pk_tickets PRIMARY KEY ( id ),
	CONSTRAINT fk_tickets_customers FOREIGN KEY ( customer_id ) REFERENCES "public".customers( id )   ,
	CONSTRAINT fk_tickets_screening FOREIGN KEY ( screening_id ) REFERENCES "public".screenings( id )   ,
	CONSTRAINT fk_tickets_seats FOREIGN KEY ( seat_id ) REFERENCES "public".seats( id )   
 );

CREATE  TABLE "public".pricing_rules ( 
	id                   serial  NOT NULL  ,
	screening_id         integer  NOT NULL  ,
	seat_type_id         integer  NOT NULL  ,
	modifier             numeric(4,2)  NOT NULL  ,
	CONSTRAINT pk_pricing_rules PRIMARY KEY ( id ),
	CONSTRAINT fk_pricing_rules_screening FOREIGN KEY ( screening_id ) REFERENCES "public".screenings( id )   ,
	CONSTRAINT fk_pricing_rules_seat_types FOREIGN KEY ( seat_type_id ) REFERENCES "public".seat_types( id )   
 );