<?php

session_start();
$sessionId = session_id();

$lastVisit = $_SESSION['LAST_VISIT'] ?? null;
$_SESSION['LAST_VISIT'] = date(DATE_ATOM);

$memcached = new Memcache();
$memcached->addServer('memcached', 11211);

$CACHE_KEY = "{$sessionId}_LAST_VISIT";
$lastVisitFromMemcached = $memcached->get($CACHE_KEY);
$memcached->set($CACHE_KEY, $_SESSION['LAST_VISIT']);

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
</body>
</html>