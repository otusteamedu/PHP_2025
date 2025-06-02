<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Генерация финансового отчета</title>
</head>
<body>
<h1>Выберете временной промежуток для выгрузки финансового отчета</h1>

<form action="/" method="get" class="form-example">
    <div class="form-example">
        Дата с: <input required type="date" name="date_from"/><br />
    </div><br />

    <div class="form-example">
        Дата по: <input required type="date" name="date_to"/><br />
    </div>

    <div class="form-example"><br />
        <input type="submit" value="Генерировать отчет" />
    </div>
</form>


</body>
</html>