CREATE TABLE job (
    id SERIAL PRIMARY KEY,
    parameters JSONB NOT NULL,
    status VARCHAR(50) NOT NULL,
    statement VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_job_status ON job(status);

CREATE INDEX idx_job_statement ON job(statement);


CREATE TABLE bankstatement (
    id SERIAL PRIMARY KEY,
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE job
ADD CONSTRAINT fk_job_statement
FOREIGN KEY (statement)
REFERENCES bankstatement(id);