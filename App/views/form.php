<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Проверка через форму</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container py-2">
        <?php if ($alert) { ?>
        <div class="alert alert-primary" role="alert"><?= $alert ?></div>
        <?php } ?>

        <?php if (!$isMethodPost) { ?>
        <form action="/" method="post">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Введите набор символов круглых скобок</label>
                <input type="text" class="form-control" id="exampleFormControlInput1" name="string">
            </div>
            <button class="btn btn-primary" type="submit">Проверить</button>
        </form>
        <?php } ?>
    </div>
</body>
</html>
