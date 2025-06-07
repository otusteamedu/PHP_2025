<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Infrastructure health check</title>
</head>
<body>
    <h1>Infrastructure health check</h1>
    <table>
        <tr>
            <th>Service</th>
            <th>Message</th>
        </tr>
        <tr>
            <td>Postgres</td>
            <td><?php echo $postgresMsg; ?></td>
        </tr>
        <tr>
            <td>Redis</td>
            <td><?php echo $redisMsg; ?></td>
        </tr>
        <tr>
            <td>Memcached</td>
            <td><?php echo $memcachedMsg; ?></td>
        </tr>
    </table>
</body>
</html>
