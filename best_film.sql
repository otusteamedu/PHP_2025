SELECT movies.ID, movies.TITLE, SUM(tickets.TICKET_COST) as total_revenue
FROM movies
JOIN movie_sessions ON movies.ID = movie_sessions.MOVIES_ID
JOIN tickets ON movie_sessions.id = tickets.SESSION_ID
GROUP BY movies.ID, movies.TITLE
ORDER BY total_revenue DESC
