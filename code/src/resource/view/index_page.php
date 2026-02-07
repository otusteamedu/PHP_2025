<html>
<body>
<section class="container">
    <?php if (!empty($users)): ?>
        <h2>Список пользователей</h2>
        <ul>
            <?php foreach ($users as $user): ?>
                <li>
                    <?= htmlspecialchars($user->getName(), ENT_QUOTES, 'UTF-8') ?>
                    <?= $user->getSurname() ? htmlspecialchars(' ' . $user->getSurname(), ENT_QUOTES, 'UTF-8') : '' ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Пользователи не найдены.</p>
    <?php endif; ?>
</section>
</body>
</html>
