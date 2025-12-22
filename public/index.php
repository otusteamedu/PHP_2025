<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Validator</title>
</head>
<body>
    <h1>Email Validator</h1>
    <form method="POST" action="api.php">
        <label for="emails">Введите email-ы (по одной в каждой строке):</label><br>
        <textarea name="emails" id="emails" rows="5" cols="30"></textarea><br>
        <button type="submit">Проверить</button>
    </form>
</body>
</html>