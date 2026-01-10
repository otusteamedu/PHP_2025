SELECT m.`id`, m.`name`, SUM(t.`price`) as `profit`
FROM `movies` m
INNER JOIN `sessions` s ON s.`movie_id` = m.`id`
INNER JOIN `tickets` t ON t.`session_id` = s.`id`
INNER JOIN `orders` o ON t.`order_id` = o.`id`
WHERE o.`status` = 'paid'
GROUP BY m.`id`, m.`name`
ORDER BY `profit` DESC
LIMIT 1;