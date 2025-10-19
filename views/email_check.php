<?php
/**
 * @var string $emailsInput - Введенные пользователем email адреса
 * @var array<int, array{email: string, isValid: bool, details: array<int, string>}> $results - Результаты валидации email адресов
 */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проверка Email адресов</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Проверка Email адресов</h1>
        <form method="post" action="/">
            <div class="mb-3">
                <label for="emails" class="form-label">Введите Email адреса (через пробел, запятую или с новой строки):</label>
                <textarea class="form-control" id="emails" name="emails" rows="10"><?= htmlspecialchars($emailsInput); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Проверить</button>
        </form>

        <?if (! empty($results)): ?>
            <h2 class="mt-5">Результаты проверки:</h2>
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Email</th>
                        <th>Результат</th>
                        <th>Детали</th>
                    </tr>
                </thead>
                <tbody>
                <?foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td>
                            <?if ($row['isValid']): ?>
                                <span class="badge bg-success">Валидный</span>
                            <?else: ?>
                                <span class="badge bg-danger">Невалидный</span>
                            <?endif; ?>
                        </td>
                        <td>
                            <?= implode('<br>', $row['details']); ?>
                        </td>
                    </tr>
                <?endforeach; ?>
                </tbody>
            </table>
        <?endif; ?>
    </div>
</body>
</html>
