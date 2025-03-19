CREATE INDEX idx_date_of_purchase ON tickets (date_of_purchase);
-- DROP INDEX idx_date_of_purchase ON tickets;

ALTER TABLE screenings DROP PRIMARY KEY, ADD PRIMARY KEY (id, datetime) USING BTREE;