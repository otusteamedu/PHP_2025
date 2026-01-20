<?php
Bitrix\Main\Loader::registerAutoloadClasses(
    'telegram.bot.access',
    [
        'Telegram\\Bot\\Access\\UserManager' => 'lib/UserManager.php',
    ]
);
?>