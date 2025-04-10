<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Search Results</title>
</head>
<body>
<h1>Результаты поиска</h1>
<?php if (empty($hits)): ?>
  <p>Книги не найдены по данному запросу</p>
<?php else: ?>
  <table border="1">
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Category</th>
      <th>Price</th>
      <th>Stock</th>
      <th>Score</th>
    </tr>
    <?php foreach ($hits as $hit): ?>
      <?php
      $source = $hit['_source'];
      $totalStock = array_sum(array_column($source['stock'], 'stock'));
      ?>
      <tr>
        <td><?php echo $hit['_id']; ?></td>
        <td><?php echo $source['title']; ?></td>
        <td><?php echo $source['category']; ?></td>
        <td><?php echo $source['price']; ?> руб.</td>
        <td><?php echo $totalStock; ?></td>
        <td><?php echo number_format($hit['_score'], 2); ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>
</body>
</html>