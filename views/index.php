<!DOCTYPE html>
<html>
<head>
    <title>Homework 15</title>
</head>
<body>

<?if (!isset($response)) {?>
<form action="/" enctype="multipart/form-data" method="post">
    <input type="date" name="dateFrom" required>
    <input type="date" name="dateTo" required>
    <input type="email" name="email" required>
    <button type="submit">Отправить</button>
</form>
<?} else {?>
    <?php echo $response->getMessage();?>
<?}?>
</body>
</html>