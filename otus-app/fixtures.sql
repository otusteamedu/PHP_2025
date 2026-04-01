insert into otus_cinema_db.cinema (id, name, info, isActive, createdAt, updatedAt)
values (1, 'cinema1', '', 1, now(), now()),
       (2, 'cinema2', 'some info', 1, now(), now()),
       (3, 'cinema3', 'test', 0, now(), now());

insert into otus_cinema_db.customer (id, email, birthDate, name, password, createdAt, updatedAt)
values (1, 'testemail1', '2001-01-01', 'some name1', 'hashedpass1', now(), now()),
       (2, 'testemail2', '2002-02-02', 'some name2', 'hashedpass2', now(), now()),
       (3, 'testemail3', '2003-03-03', 'some name3', 'hashedpass3', now(), now());

insert into otus_cinema_db.hall (id, name, cinemaId, createdAt, updatedAt)
values (1, 'some hall name1', 1, now(), now()),
       (2, 'some hall name2', 2, now(), now()),
       (3, 'some hall name3', 1, now(), now());

insert into otus_cinema_db.movie (id, name, duration, age, createdAt, updatedAt)
values (1, 'some movie name1', 3601, 16, now(), now()),
       (2, 'some movie name2', 3602, 18, now(), now()),
       (3, 'some movie name3', 3603, 0,  now(), now());

insert into otus_cinema_db.paymentMethod (id, name, isActive, createdAt, updatedAt)
values (1, 'some method', 1, now(), now());

insert into otus_cinema_db.payment (id, totalPrice, customerId, status, externalId, paymentMethodId, createdAt, updatedAt)
values (1, 100000, 1, 'paid', 'num 1', 1, now(), now()),
       (2, 102000, 2, 'paid', 'num 12', 1, now(), now()),
       (3, 208000, 3, 'paid', 'num 23', 1, now(), now()),
       (4, 100000, 2, 'failed', 'num 74', 1, now(), now()),
       (5, 102000, 2, 'paid', 'num 665', 1, now(), now()),
       (6, 124800, 3, 'paid', 'num 2891', 1, now(), now());

insert into otus_cinema_db.seatType (id, name, priceModifier, createdAt, updatedAt)
values (1, 'basic', 100, now(), now()),
       (2, 'basic+', 120, now(), now());

insert into otus_cinema_db.seat (id, seatTypeId, hallId, coordinates, number, createdAt, updatedAt)
values (1, 1, 1, '{"x": "1", "y": "1"}', '1a', now(), now()),
       (2, 1, 1, '{"x": "2", "y": "2"}', '2b', now(), now()),
       (3, 2, 1, '{"x": "3", "y": "3"}', '3c', now(), now()),
       (4, 1, 2, '{"x": "1", "y": "1"}', '1a', now(), now()),
       (5, 1, 2, '{"x": "2", "y": "2"}', '2b', now(), now()),
       (6, 2, 2, '{"x": "3", "y": "3"}', '3c', now(), now());

insert into otus_cinema_db.session (id, hallId, movieId, defaultPrice, startsAt, createdAt, updatedAt)
values (1, 1, 1, 100000, '2020-10-10 12:20:00', now(), now()),
       (2, 1, 2, 102000, '2020-10-10 15:00:00', now(), now()),
       (3, 2, 3, 104000, '2020-10-10 12:00:00', now(), now());

insert into otus_cinema_db.ticket (id, price, paymentId, sessionId, seatId, status, createdAt, updatedAt)
values (1, 100000, 1, 1, 1, 'finished', now(), now()),
       (2, 102000, 2, 2, 1, 'finished', now(), now()),
       (3, 104000, 3, 3, 4, 'finished', now(), now()),
       (4, 104000, 3, 3, 5, 'finished', now(), now()),
       (5, 102000, 5, 2, 2, 'finished', now(), now()),
       (6, 124800, 6, 3, 6, 'finished', now(), now());
