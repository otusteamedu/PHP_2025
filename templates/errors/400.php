<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Bad Request</title>
    </head>
    <body>
        <h1>Ошибка 400</h1>
        <p><?= htmlspecialchars($message ?? '', ENT_QUOTES, 'UTF-8') ?></p>
    </body>
</html>
