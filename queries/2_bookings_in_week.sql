select count(*)
from "order"
left join booking on "order".order_id = booking.order_id
where "order".order_status_id in (2, 4) and "order".created_at::date between (now() - interval '7 days') and now();
