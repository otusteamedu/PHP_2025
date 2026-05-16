<?php

/** @var string $message */
?>
<h1>Заявка принята</h1>

<div class="success">
    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
</div>

<p>
    <a href="/">Отправить еще один запрос</a>
</p>
