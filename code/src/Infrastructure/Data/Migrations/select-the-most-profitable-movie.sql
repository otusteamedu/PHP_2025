-- SQL для нахождения самого прибыльного фильма
with movie_sales as (
    select sum(final_price) as final_sum, title from tickets t
    left join sessions s on s.id = t.id_session 
    left join movies m on m.id = s.id_movie 
    where t.status = 'sold'
    group by title
)
select final_sum, title from movie_sales
WHERE final_sum = (SELECT MAX(final_sum) FROM movie_sales)