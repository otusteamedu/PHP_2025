<?php
/**
 * @var string[] $validEmails
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
    <label>Emails: <textarea name="emails"><?= htmlentities($string) ?></textarea></label>
    <button type="submit">Submit</button>
</form>
<table>
    <thead>
    <tr>
        <th><?= "Valid Emails" ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($validEmails as $email):
        ?>
        <tr>
            <td><?= $email ?></td>
        </tr>
    <?php
    endforeach;
    ?>
    </tbody>
</table>
</body>
</html>