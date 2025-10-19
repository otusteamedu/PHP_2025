-- Самый прибыльный фильм (по проданным билетам)
-- Прибыль = сумма ticket.price_paid по статусу 'sold'
with revenue as (
  select m.id as movie_id,
         m.title,
         sum(t.price_paid) as total_revenue
  from ticket t
  join showtime s on s.id = t.showtime_id
  join movie m on m.id = s.movie_id
  where t.status = 'sold'
  group by m.id, m.title
)
select movie_id, title, total_revenue
from revenue
order by total_revenue desc
limit 1;



