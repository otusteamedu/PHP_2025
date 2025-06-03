<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session storage checker</title>
</head>
<body>
    <h1>Session storage checker</h1>
    <?php if (!empty($sessionVars)): ?>
        <p>Session vars from storage</p>
        <table>
            <tr>
                <th>Session key</th>
                <th>Session value</th>
            </tr>
            <?php foreach ($sessionVars as $key => $value): ?>
                <tr>
                    <td><?php echo $key; ?></td>
                    <td><?php echo $value; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Session does not exist</p>
    <?php endif; ?>
</body>
</html>
