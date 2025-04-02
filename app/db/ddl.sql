DROP TABLE IF EXISTS players;
DROP TABLE IF EXISTS teams;

CREATE TABLE IF NOT EXISTS teams
(
    id   SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS players
(
    id      SERIAL PRIMARY KEY,
    team_id INT,
    number  INT          NOT NULL,
    name    VARCHAR(255) NOT NULL,
    age     INT          NOT NULL,
    height  INT          NOT NULL,
    weight  INT          NOT NULL,
    CONSTRAINT "fk-players-team_id" FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE SET NULL
);