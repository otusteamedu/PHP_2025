<?php

require_once __DIR__ . '/helpers.php';

/** @var string $defaultFrom */
/** @var string $defaultTo */
/** @var array<string, mixed> $data */
/** @var array<string, array<string>> $errors */

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Запрос банковской выписки</title>
        <link rel="stylesheet" href="/assets/css/report-form.css">
    </head>
    <body>
        <h1>Заказать банковскую выписку</h1>

        <?php if (!empty($errors['global'])): ?>
            <div class="form-global-error">
                <?php foreach ($errors['global'] as $error): ?>
                    <p><?= esc($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/request-report">
            <div class="form-group">
                <label for="client_name">ФИО клиента</label>
                <input type="text"
                       id="client_name"
                       name="client_name"
                       value="<?= esc($data['client_name'] ?? '') ?>"
                       required
                >
                <?php foreach ($errors['client_name'] ?? [] as $error): ?>
                    <span class="error-message"><?= esc($error) ?></span>
                <?php endforeach; ?>
            </div>

            <div class="form-group">
                <label for="date_from">Период от</label>
                <input type="date"
                       id="date_from"
                       name="date_from"
                       value="<?= esc($data['date_from'] ?? $defaultFrom) ?>"
                       max="<?= esc($defaultTo) ?>"
                       required
                >
                <?php foreach ($errors['date_from'] ?? [] as $error): ?>
                    <span class="error-message"><?= esc($error) ?></span>
                <?php endforeach; ?>
            </div>

            <div class="form-group">
                <label for="date_to">Период до</label>
                <input type="date"
                       id="date_to"
                       name="date_to"
                       value="<?= esc($data['date_to'] ?? $defaultTo) ?>"
                       max="<?= esc($defaultTo) ?>"
                       required
                >
                <?php foreach ($errors['date_to'] ?? [] as $error): ?>
                    <span class="error-message"><?= esc($error) ?></span>
                <?php endforeach; ?>
            </div>

            <div class="form-group">
                <label for="report_type">Тип выписки</label>
                <select name="report_type" id="report_type" required>
                    <option value="" disabled selected>Выберите тип</option>
                    <option
                        value="summary"
                        <?= isSelected($data['report_type'] ?? '', 'summary') ?>
                    >Краткая</option>

                    <option
                        value="detailed"
                        <?= isSelected($data['report_type'] ?? '', 'detailed') ?>
                    >Подробная</option>

                    <option
                        value="with_commissions"
                        <?= isSelected($data['report_type'] ?? '', 'with_commissions') ?>
                    >С комиссиями</option>
                </select>
                <?php foreach ($errors['report_type'] ?? [] as $error): ?>
                    <span class="error-message"><?= esc($error) ?></span>
                <?php endforeach; ?>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?= esc($data['email'] ?? '') ?>"
                       required
                >
                <?php foreach ($errors['email'] ?? [] as $error): ?>
                    <span class="error-message"><?= esc($error) ?></span>
                <?php endforeach; ?>
            </div>

            <button type="submit">Запросить выписку</button>
        </form>
        <script src="/assets/js/report-form.js" defer></script>
    </body>
</html>
