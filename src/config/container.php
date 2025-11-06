<?php
use App\Repositories\OrderLogRepository;
use App\Repositories\OrderRepository;
use App\Services\Database;
use App\Services\RabbitMQService;
use App\Views\View;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PHPMailer\PHPMailer\PHPMailer;

return [
    PDO::class => function () {
        $host = $_ENV['DB_HOST'] ?? 'host.docker.internal';
        $port = (int)($_ENV['DB_PORT'] ?? 5432);
        $dbname = $_ENV['DB_NAME'] ?? 'postgres';
        $user = $_ENV['DB_USER'] ?? 'postgres';
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $host, $port, $dbname);
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    },

    AMQPStreamConnection::class => function () {
        $host = $_ENV['RABBITMQ_HOST'] ?? 'rabbitmq';
        $port = (int)($_ENV['RABBITMQ_PORT'] ?? 5672);
        $user = $_ENV['RABBITMQ_USER'] ?? 'guest';
        $pass = $_ENV['RABBITMQ_PASS'] ?? 'guest';
        return new AMQPStreamConnection($host, $port, $user, $pass);
    },

    RabbitMQService::class => function ($c) {
        return new RabbitMQService($c->get(AMQPStreamConnection::class));
    },

    Database::class => function ($c) {
        return new Database($c->get(PDO::class));
    },

    OrderRepository::class => function ($c) {
        return new OrderRepository($c->get(Database::class));
    },

    OrderLogRepository::class => function ($c) {
        return new OrderLogRepository($c->get(Database::class));
    },

    // PHPMailer configured via .env
    PHPMailer::class => function () {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'] ?? 'localhost';
        $mail->Port = (int)($_ENV['MAIL_PORT'] ?? 25);
        $mail->SMTPAuth = (bool)($_ENV['MAIL_SMTP_AUTH'] ?? true);
        $mail->Username = $_ENV['MAIL_USERNAME'] ?? '';
        $mail->Password = $_ENV['MAIL_PASSWORD'] ?? '';
        $encryption = $_ENV['MAIL_ENCRYPTION'] ?? 'tls'; // tls|ssl|''
        if ($encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = false;
        }
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        $mail->CharSet = 'UTF-8';
        $from = $_ENV['MAIL_FROM'] ?? 'no-reply@example.local';
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Notifier';
        try {
            $mail->setFrom($from, $fromName);
        } catch (\Throwable $e) {
            // ignore invalid from address, user can set later
        }
        // Do not keep recipients between sends; each usage should add its own
        $mail->clearAllRecipients();
        return $mail;
    },

    View::class => function () {
        return new View();
    },
];
