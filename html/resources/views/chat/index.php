<?php

use Otus\Queue\Domain\Entity\Message;

/**
 * @var array<Message> $history
 */

include __DIR__ . '/../common/header.php';
?>

<!-- Name prompt overlay -->
<div class="name-overlay" id="name-overlay">
    <div class="name-card">
        <h2>👋 Добро пожаловать</h2>
        <p>Представьтесь, чтобы начать общение</p>
        <input type="text" id="name-input" placeholder="Ваше имя..." maxlength="30" autofocus>
        <button id="name-submit">Войти в чат</button>
    </div>
</div>

<div class="chat-container">
    <div class="chat-header">
        <span>💬 Чат</span>
        <span class="username-badge" id="username-badge" title="Нажмите, чтобы сменить имя"></span>
    </div>

    <div class="chat-messages" id="messages">
        <?php if (empty($history)): ?>
            <div class="empty-state" id="empty-state">Сообщений пока нет. Напишите первое!</div>
        <?php else: ?>
            <?php foreach ($history as $message): ?>
                <div class="message" data-author="<?= htmlspecialchars($message->author, ENT_QUOTES) ?>">
                    <div class="message-author"><?= htmlspecialchars($message->author, ENT_QUOTES) ?></div>
                    <div class="message-text"><?= nl2br(htmlspecialchars($message->text, ENT_QUOTES)) ?></div>
                    <div class="message-time"><?= new DateTime()->setTimestamp($message->createdAt)->format('Y-m-d H:i:s') ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <form class="chat-form" id="chat-form" action="/" method="POST">
        <textarea name="text" id="text" placeholder="Сообщение..." rows="1" required></textarea>
        <button type="submit" id="send-btn">Отправить</button>
    </form>
</div>

<?php include __DIR__ . '/../common/footer.php'; ?>
