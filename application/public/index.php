<?php

require_once __DIR__ . '/../vendor/autoload.php';

use IGainutdinov\HelloWorld;

session_start();
$sessionId = session_id();

$lastVisit = $_SESSION['LAST_VISIT'] ?? null;
$_SESSION['LAST_VISIT'] = date(DATE_ATOM);

$memcached = new Memcache();
$memcached->addServer('memcached', 11211);

$CACHE_KEY = "{$sessionId}_LAST_VISIT";
$lastVisitFromMemcached = $memcached->get($CACHE_KEY);
$memcached->set($CACHE_KEY, $_SESSION['LAST_VISIT']);

$dbInfo = null;
try {
    $db = new PDO('mysql:host=database;port=3306', getenv('DB_USER'), getenv('DB_PASSWORD'));
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $db->query('SELECT VERSION();');
    $stmt->execute();
    $dbInfo = $stmt->fetchColumn();
} catch (PDOException $e) {
    $dbInfo = "Failed to connect to database: " . $e->getMessage();
}


$helloWorld = new HelloWorld();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= "Hello World!" ?></title>
    <link rel="icon" type="image/i-icon" href="images/favicon.svg">
</head>
<body>
<h1>Hello World!</h1>
<p>Current time is <?= date(DATE_ATOM) ?></p>
<?php
if ($lastVisit): ?>
    <p>Your last visit <?= $lastVisit ?></p>
<?php
endif; ?>
<?php
if ($lastVisitFromMemcached): ?>
    <p>Your last visit from memcached <?= $lastVisitFromMemcached ?></p>
<?php
endif; ?>
<p>
    DB Version: <?= $dbInfo ?>
</p>
<?php
$name = '';
if ($_GET['name'] ?? false) {
    $name = htmlentities($_GET['name']);
    echo '<p>' . $helloWorld->getHelloString($name) . '</p>';
}
?>
<form method="get" action="/">
    <label>Your name: <input name="name" value="<?= $name ?>" type="text" max="16"/></label>
    <button type="submit">Submit</button>
</form>
</body>
</html>