-- Снятие NOT NULL с полей phone и email в таблице contacts
ALTER TABLE contacts ALTER COLUMN phone DROP NOT NULL;
ALTER TABLE contacts ALTER COLUMN email DROP NOT NULL;
