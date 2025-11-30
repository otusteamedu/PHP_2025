<?php
/** @var Auth $auth */

use Pryaniki\App\Auth;

?>
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
				<span>container_id=<?= $auth->getContainerName() ?></span>
            </h3>
			<p>redis container_id=<?= $auth->getRedisContainerId() ?></p>
            <p>Session ID: <?= $auth->getSessionId() ?></p>
            <p>Session Counter: <?= $auth->getSessionCounter() ?></p>
        </div>
    </body>
</html>