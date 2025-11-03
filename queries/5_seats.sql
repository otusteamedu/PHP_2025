select "session".session_id,
		seat.seat_id,
		seat.row_number, 
		seat.seat_number,
		case
			when booking.booking_id > 0 and "order".order_status_id != 3
			then 'Занято'
			else 'Свободно'
		end
		as booking_status
from "session"

right join seat on "session".room_id = seat.room_id
left join booking on booking.session_id = "session".session_id and booking.seat_id = seat.seat_id 
left join "order" on "order".order_id = booking.order_id
where "session".session_id = 688;