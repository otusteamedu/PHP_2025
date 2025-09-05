<?php
/**
 * @var string $message
 * @var string $string
 */

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= "Hello World!" ?></title>
    <link rel="icon" type="image/i-icon" href="favicon.svg">
</head>
<body>
<form method="post">
    <label>String: <input type="text" value="<?= htmlentities($string) ?>" name="string"></label>
    <button type="submit">Submit</button>
</form>
<?= $message ?>
</body>
</html>