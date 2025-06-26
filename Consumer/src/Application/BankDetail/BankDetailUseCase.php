<?php

namespace Consumer\Application\BankDetail;

use AMQPChannelException;
use AMQPConnectionException;
use AMQPEnvelopeException;
use AMQPExchangeException;
use AMQPQueueException;
use Consumer\Domain\DTO\BankDetailDTO;
use Consumer\Domain\Receiver\ReceiverInterface;

class BankDetailUseCase
{
    private BankDetailMailInterface $mailer;
    private ReceiverInterface $rabbitMQ;

    public function __construct(
        BankDetailMailInterface $mailer,
        ReceiverInterface $rabbitMQ
    ) {
        $this->mailer = $mailer;
        $this->rabbitMQ = $rabbitMQ;
    }

    /**
     * @return void
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPEnvelopeException
     * @throws AMQPExchangeException
     * @throws AMQPQueueException
     */
    public function run(): void {
        $callback = function ($envelope, $queue) {
            $body = $envelope->getBody();
            $data = json_decode($body, true);

            $dto = new BankDetailDTO(
                bik: $data['bik'],
                account: $data['account'],
                client: $data['client'],
                bank: $data['bank'],
            );

            echo "Доставлено: " . $body . "\n";

            $this->mailer->mail(
                $dto,
                "Данные выписки из банка",
                getenv('EMAIL_TO'),
                getenv('EMAIL_FROM')
            );

            $queue->ack($envelope->getDeliveryTag());
        };

        $this->rabbitMQ->receive($callback);
    }
}