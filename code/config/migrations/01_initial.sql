CREATE TYPE request_status AS ENUM ('pending', 'processing', 'completed', 'failed');

CREATE TABLE requests (
    id SERIAL PRIMARY KEY,
    status request_status NOT NULL DEFAULT 'pending',
    content TEXT NOT NULL,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed TIMESTAMP,
    result TEXT
);
