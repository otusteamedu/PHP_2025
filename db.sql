create type enum_hall_category as enum ('Standart', 'Comfort', 'Dolby Atmos', 'IMAX', '3D', 'VIP');
create type enum_place_type as enum ('Economy', 'Standart', 'Comfort', 'Love', 'Vip');
create type enum_film_limitation as enum ('0+', '6+', '12+', '16+', '18+');

create table сlient
(
	id 			SERIAL 	PRIMARY KEY,
	name 		VARCHAR(255),
	email 		VARCHAR(255) UNIQUE NOT NULL,
	phone 		VARCHAR(255)
);

create table ticket
(
	id			SERIAL PRIMARY KEY,
	date		DATE NOT NULL,
	client_id	INT NOT NULL REFERENCES client (id) ON DELETE CASCADE,
	session_id	INT NOT NULL REFERENCES session (id) ON DELETE CASCADE,
	place_id	INT NOT NULL REFERENCES place (id) ON DELETE CASCADE,
	price		DECIMAL(10, 2) NOT NULL
);

create table session
(
	id			SERIAL PRIMARY KEY,
	date		DATE NOT NULL,
	film_id		INT NOT NULL REFERENCES film (id) ON DELETE CASCADE,
	hall_id		INT NOT NULL REFERENCES hall (id) ON DELETE CASCADE,
);

create table film
(
	id					SERIAL PRIMARY KEY,
	sort				INT NOT NULL DEFAULT 100,
	title 				VARCHAR(255) NOT NULL,
	actors 				TEXT,
	description			TEXT,
	release				INT,
	genre				VARCHAR(255),
	limitation			enum_film_limitation
	show_period_from	DATE,
	show_period_to		DATE,
);

create table hall
(
	id			SERIAL PRIMARY KEY,
	is_active	BOOLEAN NOT NULL DEFAULT 1,
	name 		VARCHAR(255) NOT NULL,
	category	enum_hall_category
	cinema_id	INT NOT NULL REFERENCES cinema (id) ON DELETE CASCADE,
);

create table сinema
(
	id			SERIAL PRIMARY KEY,
	name 		VARCHAR(255) NOT NULL,
	city 		VARCHAR(255) NOT NULL,
	address		TEXT NOT NULL,
);

create table place
(
	id			SERIAL PRIMARY KEY,
	hall_id		INT NOT NULL REFERENCES hall (id) ON DELETE CASCADE,
	number		INT,
	range		INT,
	type		enum_place_type,
);

create table price_list
(
	date_ticket	DATE NOT NULL REFERENCES ticket (date),
	session_id	INT NOT NULL REFERENCES session (id),
	place_type	DATE NOT NULL REFERENCES place (type),
	price 		DECIMAL(10, 2) NOT NULL
);
