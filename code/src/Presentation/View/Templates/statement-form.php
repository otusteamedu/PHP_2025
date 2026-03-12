<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запрос банковской выписки</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="container">
    <h1>Банковская выписка</h1>
    <form id="statementForm">
        <div class="form-group">
            <label for="dateFrom">Дата от</label>
            <input type="date" id="dateFrom" name="dateFrom" required>
        </div>
        <div class="form-group">
            <label for="dateTo">Дата до</label>
            <input type="date" id="dateTo" name="dateTo" required>
        </div>
        <div class="form-group">
            <label for="email">Email для получения</label>
            <input type="email" id="email" name="email" placeholder="example@mail.com" required>
        </div>
        <button type="submit" id="submitBtn">Заказать выписку</button>
    </form>
    <div id="message" class="message"></div>
</div>

<script src="/assets/js/app.js"></script>
</body>
</html>
