<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;1,300&display=swap" rel="stylesheet">
    <title>Hello, Otus!</title>
    <style>
        body { font-family: "Roboto", sans-serif; font-weight: 300; }
    </style>
</head>
<body>
<div class="container flex-grow-1 text-center vh-100">
    <div class="row align-items-center vh-100">
        <div class="col">
            <h3>Hello from docker container</h3>
            {{-- В Laravel имя хоста (ID контейнера) можно получить так: --}}
            <h3>Запрос обработал контейнер: <span class="font-monospace">container_id={{ gethostname() }}</span></h3>
        </div>
    </div>
</div>
</body>
</html>
