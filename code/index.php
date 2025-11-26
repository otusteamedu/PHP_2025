<!doctype html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Hello, Otus!</title>
</head>
<body>
<div class="container">
	<h3 class="container__title">Запрос обработал контейнер:
		<span>container_id=<?php echo $_SERVER['HOSTNAME'];?></span>
	</h3>
	<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/redis.php';  ?>
	<?php if (isset($containerIdFromRedis)):?>
		<p>redis container_id=<?= $containerIdFromRedis ?></p>
	<?php endif;?>
</div>
</body>
</html>