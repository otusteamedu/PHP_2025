select movie.movie_name, movie.movie_description, "session".start_time
from "session"
left join movie on "session".movie_id = movie.movie_id
where "session".start_time::date = current_date
order by movie.movie_id;

