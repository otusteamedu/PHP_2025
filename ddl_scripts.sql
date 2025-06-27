-- DDL скрипты создания базы данных кинотеатра

CREATE TABLE "sessions" (
  "id" integer PRIMARY KEY,
  "time" time,
  "seat_id" integer,
  "film_id" integer
);

CREATE TABLE "halls" (
  "id" integer PRIMARY KEY,
  "name" varchar
);

CREATE TABLE "customers" (
  "id" integer PRIMARY KEY,
  "login" varchar,
  "password" varchar,
  "email" varchar,
  "discount" integer,
  "created_at" timestamp
);

CREATE TABLE "films" (
  "id" integer PRIMARY KEY,
  "title" varchar,
  "duration" integer,
  "created_at" timestamp
);

CREATE TABLE "customer_sessions" (
  "id" integer PRIMARY KEY,
  "session_id" integer,
  "customer_id" integer
);

CREATE TABLE "seats" (
  "id" integer PRIMARY KEY,
  "number" integer,
  "seat_category_id" integer,
  "hall_id" integer
);

CREATE TABLE "seat_category" (
  "id" integer PRIMARY KEY,
  "category_name" varchar,
  "cost" money
);

COMMENT ON COLUMN "films"."duration" IS 'Продолжительность в минутах';

ALTER TABLE "sessions" ADD FOREIGN KEY ("id") REFERENCES "customer_sessions" ("session_id");

ALTER TABLE "customer_sessions" ADD FOREIGN KEY ("customer_id") REFERENCES "customers" ("id");

ALTER TABLE "seats" ADD FOREIGN KEY ("id") REFERENCES "sessions" ("seat_id");

ALTER TABLE "seats" ADD FOREIGN KEY ("hall_id") REFERENCES "halls" ("id");

ALTER TABLE "sessions" ADD FOREIGN KEY ("film_id") REFERENCES "films" ("id");

ALTER TABLE "seats" ADD FOREIGN KEY ("seat_category_id") REFERENCES "seat_category" ("id");