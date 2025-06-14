<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helper;
use Infrastructure\Rabbit\Connection;
use PhpAmqpLib\Message\AMQPMessage;

try {
    Helper::consoleLog("Подключаемся к RabbitMQ...");

    $env = Helper::getEnv();
    $connection = new Connection($env);

    Helper::consoleLog("Ожидаем сообщения в очереди. Для выхода нажмите Ctrl+C");

    $callback = function (AMQPMessage $msg) {
        try {
            $body = json_decode($msg->getBody(), true, 512, JSON_THROW_ON_ERROR);

            Helper::consoleLog("Получено новое сообщение:", 'SUCCESS');
            Helper::consoleLog("----------------------------------------");
            Helper::consoleLog("Заголовки: " . print_r($msg->get_properties(), true));
            Helper::consoleLog("Тело сообщения:");
            print_r($body);
            Helper::consoleLog("----------------------------------------");

            $msg->ack();

            Helper::consoleLog("Сообщение успешно обработано и подтверждено", 'SUCCESS');

        } catch (JsonException $e) {
            Helper::consoleLog("Ошибка декодирования JSON: " . $e->getMessage(), 'ERROR');
            $msg->nack();
        }
    };


    $channel = $connection->getChannel();
    $channel->basic_qos(0, 1, null);

    $channel->basic_consume(
        $env['RABBITMQ_QUEUE'],
        '',
        false,
        false,
        false,
        false,
        $callback
    );

    while ($channel->is_consuming()) {
        try {
            $channel->wait();
        } catch (Exception $e) {
            Helper::consoleLog("Ошибка при ожидании сообщения: " . $e->getMessage(), 'ERROR');
            sleep(5);
        }
    }

} catch (Exception $e) {
    Helper::consoleLog("Критическая ошибка: " . $e->getMessage(), 'ERROR');
} finally {
    if (isset($channel)) {
        $channel->close();
    }
    if (isset($connection)) {
        $connection->close();
    }
    Helper::consoleLog("Работа завершена");
}