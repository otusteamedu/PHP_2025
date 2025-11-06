<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Заявка на банковскую выписку</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;max-width:720px;margin:40px auto;padding:0 16px}
        label{display:block;margin:12px 0 4px}
        input,button{padding:8px;font-size:16px}
        form{border:1px solid #ddd;border-radius:8px;padding:16px}
        button{margin-top:16px}
        .note{color:#555;font-size:14px}
    </style>
</head>
<body>
<h1>Заявка на генерацию банковской выписки</h1>
<p>Заполните форму ниже. Обработка может занимать продолжительное время. Вы получите уведомление на email после готовности отчёта.</p>
<form method="post" action="/order">
    <label for="email">Email для уведомления</label>
    <input required type="email" id="email" name="email" placeholder="you@example.com">

    <label for="date_from">Дата начала</label>
    <input required type="date" id="date_from" name="date_from">

    <label for="date_to">Дата окончания</label>
    <input required type="date" id="date_to" name="date_to">

    <button type="submit">Отправить заявку</button>
    <p class="note">Нажимая кнопку, вы подтверждаете, что согласны получить уведомление по email.</p>
</form>
</body>
</html>
