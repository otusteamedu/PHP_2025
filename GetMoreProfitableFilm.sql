select films.id as film_id, films.title as title, sum(payments.amount) as amount_sum
from payments
         join orders on payments.order_id = orders.id
         join tickets on orders.id = tickets.order_id
         join sessions on tickets.session_id = sessions.id
         join films on sessions.film_id = films.id
where payments.status = 'payed' and orders.status = 'payed'
group by films.id
order by amount_sum desc limit 1;