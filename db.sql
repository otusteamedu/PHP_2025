create type enum_hall_category as enum ('Standart', 'Comfort', 'Dolby Atmos', 'IMAX', '3D', 'VIP');
create type enum_place_type as enum ('Economy', 'Standart', 'Comfort', 'Love', 'Vip');
create type enum_film_limitation as enum ('0+', '6+', '12+', '16+', '18+');
create type enum_order_status as enum ('reserved', 'paid', 'cancelled');
create type enum_payment_is_payd as enum ('Y', 'N');

create table сlient
(
    id 			SERIAL 	PRIMARY KEY,
    name 		VARCHAR(255),
    email 		VARCHAR(255) UNIQUE NOT NULL,
    phone 		VARCHAR(255)
);

create table order
(
    id 			SERIAL 	PRIMARY KEY,
    client_id	INT NOT NULL REFERENCES client (id) ON DELETE CASCADE,
    status		enum_order_status,
    created_at  DATE NOT NULL,
    updated_at  DATE NOT NULL
);

create table payment
(
    id 			SERIAL 	PRIMARY KEY,
    order_id	INT NOT NULL REFERENCES order (id) ON DELETE CASCADE,
    is_payd		enum_payment_is_payd,
    amount		DECIMAL(10, 2) NOT NULL,
    created_at  DATE NOT NULL,
    updated_at  DATE NOT NULL
);

create table ticket
(
    id			SERIAL PRIMARY KEY,
    client_id	INT NOT NULL REFERENCES client (id) ON DELETE CASCADE,
    place_id	INT NOT NULL REFERENCES place (id) ON DELETE CASCADE,
    session_id	INT NOT NULL REFERENCES session (id) ON DELETE CASCADE,
    order_id	INT NOT NULL REFERENCES order (id) ON DELETE CASCADE,
    price		DECIMAL(10, 2) NOT NULL,
    created_at  DATE NOT NULL,
    updated_at  DATE NOT NULL
);

create table session
(
    id			SERIAL PRIMARY KEY,
    hall_id		INT NOT NULL REFERENCES hall (id) ON DELETE CASCADE,
    film_id		INT NOT NULL REFERENCES film (id) ON DELETE CASCADE,
    created_at  DATE NOT NULL,
    updated_at  DATE NOT NULL
);

create table hall
(
    id			SERIAL PRIMARY KEY,
    is_active	BOOLEAN NOT NULL DEFAULT 1,
    name 		VARCHAR(255) NOT NULL,
    category	enum_hall_category,
    cinema_id	INT NOT NULL REFERENCES cinema (id) ON DELETE CASCADE
);

create table сinema
(
    id			SERIAL PRIMARY KEY,
    name 		VARCHAR(255) NOT NULL,
    city 		VARCHAR(255) NOT NULL,
    address		TEXT NOT NULL
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
    limitation			enum_film_limitation,
    show_period_from	DATE,
    show_period_to		DATE
);

create table place
(
    id			SERIAL PRIMARY KEY,
    hall_id		INT NOT NULL REFERENCES hall (id) ON DELETE CASCADE,
    number		INT,
    range		INT,
    type		enum_place_type
);

create table price_list
(
    date_ticket	DATE NOT NULL REFERENCES ticket (date),
    session_id	INT NOT NULL REFERENCES session (id),
    place_type	DATE NOT NULL REFERENCES place (type),
    price 		DECIMAL(10, 2) NOT NULL
);
