<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Заявка принята</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;max-width:720px;margin:40px auto;padding:0 16px}
        a{color:#0b5ed7;text-decoration:none}
    </style>
</head>
<body>
<h1>Заявка принята в обработку</h1>
<p>Номер вашей заявки: <strong><?= htmlspecialchars((string)($id ?? '')) ?></strong>.</p>
<p>Мы отправим уведомление на <strong><?= htmlspecialchars((string)($email ?? '')) ?></strong> после подготовки отчёта.</p>
<p><a href="/">Вернуться к форме</a></p>
</body>
</html>
