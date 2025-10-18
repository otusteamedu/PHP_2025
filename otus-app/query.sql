select
    m.id,
    m.name,
    sum(s.defaultPrice * st.priceModifier) / (100 * 100) as totalSum
from otus_cinema_db.movie m
         join otus_cinema_db.session s on m.id = s.movieId
         join otus_cinema_db.ticket t on s.id = t.sessionId
         join otus_cinema_db.seat s2 on t.seatId = s2.id
         join otus_cinema_db.seatType st on st.id = s2.seatTypeId
where t.status = 'finished'
group by m.id
order by totalSum desc
limit 1
;

-- with limit 1:
-- 3,some movie name3,3328.0000 (1040 * 1.2 + 1040 * 1 + 1040 * 1)

-- without limit 1:
-- 3,some movie name3,3328.0000 (1040 * 1.2 + 1040 * 1 + 1040 * 1)
-- 2,some movie name2,2040.0000 (1020 * 1 + 1020 * 1)
-- 1,some movie name1,1000.0000 (1000 * 1)
