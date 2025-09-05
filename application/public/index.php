<?php

use App\Base\Application;

require __DIR__ . '/../vendor/autoload.php';

$app = Application::getInstance();
$app->run();

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

function validate_brackets(string $brackets): bool
{
    $countOpenBrackets = preg_match_all("/\(/u", $brackets);
    $countCloseBrackets = preg_match_all("/\)/u", $brackets);
    return $countOpenBrackets === $countCloseBrackets;
}

$string = $_POST['string'] ?? false;
$stringValidationMessage = null;
if ($string) {
    if (!validate_brackets($string)) {
        http_response_code(400);
        $stringValidationMessage = "Invalid string value";
    } else {
        $stringValidationMessage = "Success";
    }
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title><?= "Hello World!" ?></title>
    <link rel="icon" type="image/i-icon" href="favicon.svg">
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
<form method="post">
    <label>String: <input type="text" value="<?= htmlentities($string) ?>" name="string"></label>
    <button type="submit">Submit</button>
</form>
<?= $stringValidationMessage ?>
</body>
</html>