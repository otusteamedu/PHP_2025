<?php

/** @var string[] $errors */
/** @var array<string, string> $previousInput */
?>
<h1>Заказ банковской выписки</h1>

<?php if ($errors !== []): ?>
    <div class="errors">
        <strong>Проверьте данные формы:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="/">
    <label for="email">Email для получения выписки</label>
    <input
        id="email"
        name="email"
        type="email"
        value="<?= htmlspecialchars($previousInput['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >
    <div class="hint">Поле необязательное. Если email не указан, выписку можно будет забрать в отделении.</div>

    <label for="account_number">Номер счета</label>
    <input
        id="account_number"
        name="account_number"
        type="text"
        inputmode="numeric"
        required
        value="<?= htmlspecialchars($previousInput['account_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <label for="date_from">Дата начала периода</label>
    <input
        id="date_from"
        name="date_from"
        type="date"
        required
        value="<?= htmlspecialchars($previousInput['date_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <label for="date_to">Дата окончания периода</label>
    <input
        id="date_to"
        name="date_to"
        type="date"
        required
        value="<?= htmlspecialchars($previousInput['date_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <button type="submit">Отправить запрос</button>
</form>
