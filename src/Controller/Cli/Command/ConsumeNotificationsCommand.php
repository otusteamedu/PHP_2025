<?php

declare(strict_types=1);

namespace App\Controller\Cli\Command;

use App\Controller\Amqp\OrderNotification\ConsumerSettings;
use App\Controller\Amqp\OrderNotification\OrderNotificationConsumer;
use App\Domain\OrderNotification\Contract\NotificationTaskWarningListenerInterface;
use App\Domain\OrderNotification\NotificationTaskService;
use App\Domain\OrderNotification\ValueObject\Task\NotificationChannel;
use App\Infrastructure\RabbitMq\Config\NotificationAmqpConfig;
use App\Infrastructure\RabbitMq\Connection\AmqpConnectionInterface;
use App\Infrastructure\RabbitMq\Exception\AmqpConnectionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand('rabbitmq:consume:notifications')]
class ConsumeNotificationsCommand extends Command
{
    public function __construct(
        private readonly AmqpConnectionInterface $connection,
        private readonly NotificationAmqpConfig $config,
        private readonly NotificationTaskService $taskService,
        private readonly NotificationTaskWarningListenerInterface $warningListener,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                name: 'channel',
                mode: InputArgument::REQUIRED,
                description: 'Notification channel to consume: email or sms',
            )
            ->addOption(
                name: 'max-attempts',
                mode: InputOption::VALUE_REQUIRED,
                description: 'Max retry attempts',
                default: $this->config->maxAttempts(),
            )
            ->addOption(
                name: 'prefetch-count',
                mode: InputOption::VALUE_REQUIRED,
                description: 'QoS prefetch count',
                default: $this->config->prefetchCount(),
            )
        ;
    }

    public function __invoke(InputInterface $input): int
    {
        try {
            $settings = $this->resolveSettings($input);

            $consumer = new OrderNotificationConsumer(
                taskService: $this->taskService,
                warningListener: $this->warningListener,
                connection: $this->connection,
                queueName: $settings->queueName,
                maxAttempts: $settings->maxAttempts,
                prefetchCount: $settings->prefetchCount,
            );

            $consumer->consume();

            return Command::SUCCESS;

        } catch (AmqpConnectionException $e) {
            fwrite(STDERR, ' [CONNECTION] ' . $e->getMessage() . "\n");
            return Command::FAILURE;

        } catch (\InvalidArgumentException $e) {
            fwrite(STDERR, ' [INVALID] ' . $e->getMessage() . "\n");
            return Command::INVALID;

        } catch (\Throwable $e) {
            fwrite(STDERR, ' [ERROR] ' . $e->getMessage() . "\n");
            $this->printPreviousChain($e);
            return Command::FAILURE;
        }
    }

    private function resolveSettings(InputInterface $input): ConsumerSettings
    {
        $channel = $this->requireChannel($input);

        return new ConsumerSettings(
            queueName: $this->resolveQueueName($channel),
            maxAttempts: $this->resolvePositiveInt($input, 'max-attempts'),
            prefetchCount: $this->resolvePositiveInt($input, 'prefetch-count'),
        );
    }

    private function requireChannel(InputInterface $input): NotificationChannel
    {
        $channelArg = (string) $input->getArgument('channel');
        $channel = NotificationChannel::tryFrom($channelArg);
        if ($channel === null) {
            throw new \InvalidArgumentException(
                sprintf("Unknown channel '%s'. Use 'email' or 'sms'.", $channelArg),
            );
        }

        return $channel;
    }

    private function resolveQueueName(NotificationChannel $channel): string
    {
        return match ($channel) {
            NotificationChannel::Email => $this->config->emailQueue(),
            NotificationChannel::Sms => $this->config->smsQueue(),
        };
    }

    private function resolvePositiveInt(InputInterface $input, string $optionName): int
    {
        $value = (int) $input->getOption($optionName);
        if ($value < 1) {
            throw new \InvalidArgumentException(
                sprintf("Option '--%s' must be positive integer", $optionName),
            );
        }

        return $value;
    }

    private function printPreviousChain(\Throwable $e): void
    {
        $previous = $e->getPrevious();
        while ($previous !== null) {
            fwrite(STDERR, ' [DETAILS] ' . $previous->getMessage() . "\n");
            $previous = $previous->getPrevious();
        }
    }
}
