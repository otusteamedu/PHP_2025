<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Book Search</title>
</head>
<body>
<h1>Поиск книг</h1>
<form method="get">
  <label for="query">Поисковый запрос:</label>
  <input type="text" id="query" name="query"><br><br>

  <label for="category">Категория:</label>
  <input type="text" id="category" name="category"><br><br>

  <label for="max-price">Максимальная цена:</label>
  <input type="number" id="max-price" name="max-price" step="0.01"><br><br>

  <label for="in-stock">Только в наличии:</label>
  <input type="checkbox" id="in-stock" name="in-stock"><br><br>

  <input type="submit" value="Искать">
</form>
</body>
</html>