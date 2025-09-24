<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Validator</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form name="form" class="container" action="api.php" method="post">
        <h1>Email Validator</h1>
        <label for="emails">Enter email addresses (one per line) to validate:</label>
        <div style="display: block;box-sizing: border-box;margin-right: 20px;">
            <textarea id="emails" name="emails" placeholder="example@domain.com;&#10;test@nonexistentdomain.xyz;&#10;user@gmail.com"></textarea>
        </div>
        <br>
        <button type="submit">Validate Emails</button>
    </form>

    <div id="results"></div>

    <script src="script.js" defer></script>
</body>
</html>