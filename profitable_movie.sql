select sm.movie_id, sum(actual_price) as sum from ticket
    left join session_movie sm on sm.id = ticket.session_movie_id
    left join movie m on m.id = sm.movie_id
where status = 'куплен' or status='использован'
group by sm.movie_id
order by sum desc
limit 1