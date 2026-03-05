<?php
    require_once __DIR__ . "/src/Controller.php";

    $request = new Controller();
    $request->handleRequest();

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;1,300&display=swap" rel="stylesheet">

    <title>Hello, Otus!</title>

    <style>
        body {
            font-family: "Roboto", sans-serif;
            font-optical-sizing: auto;
            font-weight: 300;
            font-style: normal;
            font-variation-settings: "wdth" 100;
        }
    </style>
</head>
<body>
<div class="container flex-grow-1 text-center vh-100">
    <div class="row align-items-center vh-100">
        <div class="col">
            <h3>Hello from docker container</h3>
            <h3>Запрос обработал контейнер: <span class="font-monospace">container_id=<?php echo $_SERVER['HOSTNAME'];?></span></h3>
        </div>
    </div>
</div>

<script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>

