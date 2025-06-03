<?php header('Cache-Control: no-cache'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Main page</title>
</head>
<body>
    <h1>Welcome to main page</h1>
    <h2>Nginx balancer health check</h2>
    <p>Hostname (php container): <?php echo $_SERVER['HOSTNAME']; ?></p>
    <p>Server_addr (nginx node ip): <?php echo $_SERVER['SERVER_ADDR']; ?></p>
</body>
</html>
