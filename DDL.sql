CREATE SCHEMA IF NOT EXISTS "cinema";

CREATE TABLE "cinema".customers
(
    "ID"            serial         NOT NULL,
    "NAME"          varchar(100)   NOT NULL,
    "EMAIL"         varchar(120),
    "PHONE"         varchar(50)    NOT NULL,
    "REGISTER_DATA" date           NOT NULL,
    CONSTRAINT "PK_CUSTOMERS" PRIMARY KEY ("ID"),
    CONSTRAINT "EMAIL" UNIQUE ("EMAIL")
);

CREATE TABLE "cinema".halls
(
    "ID"          serial         NOT NULL,
    "NAME"        varchar(100) NOT NULL,
    "CAPACITY"    integer      NOT NULL,
    "DESCRIPTION" text,
    CONSTRAINT "PK_HALLS" PRIMARY KEY ("ID")
);

CREATE TABLE "cinema".movies
(
    "ID"           serial         NOT NULL,
    "TITLE"        varchar(200)   NOT NULL,
    "DURATION"     smallint       NOT NULL,
    "GENRE"        varchar(50)    NOT NULL,
    "RELEASE_DATE" date           NOT NULL,
    CONSTRAINT "PK_MOVIES" PRIMARY KEY ("ID")
);

CREATE TABLE "cinema".screenings
(
    "ID"              serial       NOT NULL,
    "MOVIE_ID"        integer      NOT NULL,
    "HALL_ID"         integer      NOT NULL,
    "SCREENING_START" timestamp    NOT NULL,
    "SCREENING_END"   timestamp    NOT NULL,
    "BASE_PRICE"      smallint     NOT NULL,
    CONSTRAINT "PK_SCREENING" PRIMARY KEY ("ID"),
    CONSTRAINT "FK_SCREENING_MOVIES" FOREIGN KEY ("MOVIE_ID") REFERENCES "cinema".movies ("ID"),
    CONSTRAINT "FK_SCREENING_HALLS" FOREIGN KEY ("HALL_ID") REFERENCES "cinema".halls ("ID")
);

CREATE TABLE "cinema".seat_types
(
    "ID"    serial        NOT NULL,
    "TITLE" varchar(50)   NOT NULL,
    CONSTRAINT "PK_SEAT_TYPES" PRIMARY KEY ("ID")
);

CREATE TABLE "cinema".seats
(
    "ID"           serial      NOT NULL,
    "ROW_NUMBER"   smallint    NOT NULL,
    "SEAT_NUMBER"  smallint    NOT NULL,
    "SEAT_TYPE_ID" integer     NOT NULL,
    "HALL_ID"      integer     NOT NULL,
    CONSTRAINT "PK_SEATS" PRIMARY KEY ("ID"),
    CONSTRAINT "FK_SEATS_SEAT_TYPES" FOREIGN KEY ("SEAT_TYPE_ID") REFERENCES "cinema".seat_types ("ID"),
    CONSTRAINT "FK_SEATS_HALLS" FOREIGN KEY ("HALL_ID") REFERENCES "cinema".halls ("ID")
);

CREATE TABLE "cinema".tickets
(
    "ID"            serial     NOT NULL,
    "CUSTOMER_ID"   integer    NOT NULL,
    "SCREENING_ID"  integer    NOT NULL,
    "SEAT_ID"       integer    NOT NULL,
    "PRICE"         smallint   NOT NULL,
    "PURCHASE_DATE" timestamp  NOT NULL,
    CONSTRAINT "PK_TICKETS" PRIMARY KEY ("ID"),
    CONSTRAINT "FK_TICKETS_CUSTOMER" FOREIGN KEY ("CUSTOMER_ID") REFERENCES "cinema".customers ("ID"),
    CONSTRAINT "FK_TICKETS_SCREENING" FOREIGN KEY ("SCREENING_ID") REFERENCES "cinema".screenings ("ID"),
    CONSTRAINT "FK_TICKETS_SEATS" FOREIGN KEY ("SEAT_ID") REFERENCES "cinema".seats ("ID")
);

CREATE TABLE "cinema".pricing_rules
(
    "ID"           serial         NOT NULL,
    "SCREENING_ID" integer        NOT NULL,
    "SEAT_TYPE_ID" integer        NOT NULL,
    "MODIFIER"     numeric(4, 2)  NOT NULL,
    CONSTRAINT "PK_PRICING_RULES" PRIMARY KEY ("ID"),
    CONSTRAINT "FK_PRICING_RULES_SCREENING" FOREIGN KEY ("SCREENING_ID") REFERENCES "cinema".screenings ("ID"),
    CONSTRAINT "FK_PRICING_RULES_SEAT_TYPES" FOREIGN KEY ("SEAT_TYPE_ID") REFERENCES "cinema".seat_types ("ID")
);