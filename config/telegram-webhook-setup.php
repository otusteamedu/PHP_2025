<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Infrastructure\Config\AppConfig;
use App\Infrastructure\Logging\Logger;

AppConfig::load();
$logger = Logger::getInstance();

$botToken = AppConfig::getTelegramBotToken();

if (empty($botToken)) {
    $logger->error('Telegram bot token not found in .env file');
    exit(1);
}

$logger->info('Setting up Telegram webhook');

// получаем HTTPS URL от ngrok
try {
    $ngrokApiUrl = AppConfig::getNgrokApiUrl();
    $response = file_get_contents($ngrokApiUrl . '/api/tunnels');
    if ($response === false) {
        $logger->error('Failed to get ngrok tunnels', ['api_url' => $ngrokApiUrl]);
        exit(1);
    }

    $tunnels = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    $httpsUrl = null;

    if (!empty($tunnels['tunnels'])) {
        foreach ($tunnels['tunnels'] as $tunnel) {
            if ($tunnel['proto'] === 'https') {
                $httpsUrl = $tunnel['public_url'];
                break;
            }
        }
    }

    if (!$httpsUrl) {
        $logger->error('HTTPS tunnel not found in ngrok');
        exit(1);
    }

    $webhookUrl = $httpsUrl . '/api/telegram/webhook';
    $logger->info('Setting webhook URL', ['url' => $webhookUrl]);

    // устанавливаем webhook
    $url = "https://api.telegram.org/bot{$botToken}/setWebhook";
    $data = [
        'url' => $webhookUrl,
        'allowed_updates' => ['message']
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

    if ($httpCode === 200 && $result['ok']) {
        $logger->info('Webhook set successfully', ['url' => $webhookUrl]);
    } else {
        $logger->error('Failed to set webhook', ['response' => $result]);
        exit(1);
    }
} catch (Exception $e) {
    $logger->error('Error setting webhook: ' . $e->getMessage());
    exit(1);
}
