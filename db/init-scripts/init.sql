CREATE USER 'otus_user'@'%' IDENTIFIED BY 'otus_user_password';

GRANT ALL PRIVILEGES ON otus_db.* TO 'otus_user'@'%';

FLUSH PRIVILEGES;

CREATE TABLE IF NOT EXISTS todo
(
    id
    INT
    AUTO_INCREMENT
    PRIMARY
    KEY,
    title
    VARCHAR
(
    50
) NOT NULL,
    description VARCHAR
(
    2000
) DEFAULT NULL,
    created_at DATETIME NOT NULL
    );

INSERT INTO todo (title, description, created_at)
VALUES ('Task 1', 'Some big task 1', '2025-02-16 00:00:00');

INSERT INTO todo (title, description, created_at)
VALUES ('Task 2', 'Some big task 2', '2025-02-16 00:00:00');

