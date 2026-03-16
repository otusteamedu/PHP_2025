CREATE TABLE Event
(
    id   int primary key,
    name varchar(50)
);

CREATE TABLE EventConfig
(
    id      int primary key,
    config  varchar(255),
    eventId int,
    foreign key (eventId) references Event (id)
);

INSERT INTO Event (id, name)
VALUES (1, 'Event 1'),
       (2, 'Event 2'),
       (3, 'Event 3');

INSERT INTO EventConfig (id, config, eventId)
VALUES (1, 'Some config 1', 1),
       (2, 'Some config 2', 1),
       (3, 'Some config 3', 2);
