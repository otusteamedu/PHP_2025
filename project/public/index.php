<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Session\RedisSessionManager;

// Если пришёл POST-запрос, обрабатываем его через EmailVerifierApp
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../EmailVerifierApp.php';
    exit;
}

$csrfToken = (new RedisSessionManager())->generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verifier</title>
</head>
<body>
<h1>Email Verifier</h1>
<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
    <label for="emails">Enter emails (one per line):</label><br>
    <textarea name="emails" id="emails" rows="10" cols="30"></textarea><br>
    <button type="submit">Verify</button>
</form>
</body>
</html>
