select sm.movie_id, m.name,sum(pl.price) as sum from ticket
    left join session_movie sm on sm.id = ticket.session_movie_id
    left join movie m on m.id = sm.movie_id
    left join shema_room sr on sr.id = ticket.shema_id
    left join price_list pl on ticket.session_movie_id  = pl.session_movie_id and  pl.type_id  = sr.type_id
where status = 'куплен' or status='использован'
group by sm.movie_id, m.name
order by sum desc
limit 1