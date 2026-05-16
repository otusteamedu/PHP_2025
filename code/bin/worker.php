<?php

declare(strict_types=1);

/**
 * Консольный worker для обработки заявок на банковскую выписку из очереди RabbitMQ.
 */

use App\Delivery\StatementDeliveryFactoryProvider;
use App\Mail\MailerFactory;
use App\Queue\QueueConsumerFactory;
use App\Service\StatementRequestMessageHandler;

require dirname(__DIR__) . '/vendor/autoload.php';

$mailer = (new MailerFactory())->create();
$deliveryFactory = (new StatementDeliveryFactoryProvider($mailer))->create();

$messageHandler = new StatementRequestMessageHandler($deliveryFactory);

$consumer = (new QueueConsumerFactory())->create();
$consumer->consume([$messageHandler, 'handle']);
