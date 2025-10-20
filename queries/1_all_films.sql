select distinct movie.movie_id, movie.movie_name, movie.movie_description
from "session"
left join movie on "session".movie_id = movie.movie_id
where "session".start_time::date = current_date
order by movie.movie_id;
