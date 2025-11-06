<?php
declare(strict_types=1);

use App\Repositories\OrderLogRepository;
use App\Services\RabbitMQService;
use DI\ContainerBuilder;
use PHPMailer\PHPMailer\PHPMailer;

require __DIR__ . '/../vendor/autoload.php';

$envPath = dirname(__DIR__);
if (file_exists($envPath . '/.env')) {
    Dotenv\Dotenv::createImmutable($envPath)->load();
}

$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(require __DIR__ . '/../src/config/container.php');
$container = $containerBuilder->build();

/** @var RabbitMQService $mq */
$mq = $container->get(RabbitMQService::class);
/** @var OrderLogRepository $logs */
$logs = $container->get(OrderLogRepository::class);

echo "[INFO] Notification consumer started. Waiting for messages..." . PHP_EOL;

$mq->consume(RabbitMQService::NOTIFICATIONS_QUEUE, function (array $msg) use ($logs, $container) {
    $orderId = (int)($msg['order_id'] ?? 0);
    $email = (string)($msg['email'] ?? '');
    $status = (string)($msg['status'] ?? 'ready');

    $subject = "Ваш отчёт готов";
    $body = sprintf("Отчёт по заявке #%d готов. Статус: %s.", $orderId, $status);

    try {
        /** @var PHPMailer $mailer */
        $mailer = $container->get(PHPMailer::class);
        $mailer->clearAllRecipients();
        if (!isset($_ENV['MAIL_FROM'])) {
            // Ensure From is set if not set in DI
            try { $mailer->setFrom('no-reply@example.local', 'Notifier'); } catch (\Throwable $e) {}
        }
        $mailer->addAddress($email);
        $mailer->isHTML(false);
        $mailer->Subject = $subject;
        $mailer->Body = $body;

        $success = $mailer->send();
        $message = $success ? 'OK' : 'Mailer returned false';
        echo sprintf("[MAIL] to=%s subject=\"%s\" sent=%s\n", $email, $subject, $success ? 'true' : 'false');
    } catch (\Throwable $e) {
        $success = false;
        $message = 'Send failed: ' . $e->getMessage();
        echo sprintf("[ERROR] Email send failed for %s: %s\n", $email, $e->getMessage());
    }

    if ($orderId > 0 && $email !== '') {
        $logs->log($orderId, $email, $success, $message);
    }
});
