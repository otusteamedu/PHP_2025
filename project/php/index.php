<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verifier</title>
</head>
<body>
<h1>Email Verifier</h1>
<form method="POST" action="/">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCsrfToken()); ?>">
    <label for="emails">Enter emails (one per line):</label><br>
    <textarea name="emails[]" rows="5" cols="50"></textarea><br>
    <button type="submit">Verify</button>
</form>
</body>
</html>