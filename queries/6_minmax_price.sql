select min(booking.booking_price) as min_price, max(booking.booking_price) as max_price
from 
    booking
where
    booking.session_id = 1;
