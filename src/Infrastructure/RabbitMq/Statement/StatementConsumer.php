<?php
declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Statement;

use App\Application\UseCase\ProcessStatementUseCase;
use App\Infrastructure\RabbitMq\RabbitMqChannelProvider;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class StatementConsumer
{
    public function __construct(
        private readonly RabbitMqChannelProvider $channels,
        private readonly ProcessStatementUseCase $useCase,
        private readonly LoggerInterface $logger,
    ) {}

    /**
     * @return void
     * @throws \Throwable
     */
    public function execute(): void
    {
        $channel = $this->channels->channel();
        $channel->queue_declare(StatementProducer::QUEUE_NAME, false, true, false, false);

        $this->logger->info('Waiting messages', ['queue' => StatementProducer::QUEUE_NAME]);

        $channel->basic_qos(0, 1, false);

        $channel->basic_consume(
            queue: StatementProducer::QUEUE_NAME,
            consumer_tag: '',
            no_local: false,
            no_ack: false,
            exclusive: false,
            nowait: false,
            callback: function (AMQPMessage $message): void {
                $messageBody = $message->getBody();

                try {
                    $this->useCase->handle($messageBody);
                    $message->ack();
                } catch (\JsonException $exception) {
                    $this->logger->warning('Bad JSON', ['error' => $exception->getMessage()]);
                    $message->reject(false);
                } catch (\InvalidArgumentException $exception) {
                    $this->logger->warning('Invalid payload', ['error' => $exception->getMessage()]);
                    $message->reject(false);
                } catch (\Throwable $exception) {
                    $this->logger->error('Job failed', ['error' => $exception]);
                    $message->nack(false, true);
                }
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $this->channels->close();
    }
}
