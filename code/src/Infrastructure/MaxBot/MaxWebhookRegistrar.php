<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\MaxBot;

use MaxMessenger\Bot\MaxApiClient;
use MaxMessenger\Bot\Models\Enums\UpdateType;
use MaxMessenger\Bot\Models\Requests\SubscriptionRequestBody;
use MkdBot\Infrastructure\Interface\MaxWebhookRegistrarInterface;
use Psr\Log\LoggerInterface;

/**
 * Регистратор webhook в Max API — подписка на обновления
 */
class MaxWebhookRegistrar implements MaxWebhookRegistrarInterface
{
    public function __construct(
        private readonly MaxApiClient $client,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function subscribe(string $url, string $secret, array $updateTypes): void
    {
        $subscription = new SubscriptionRequestBody();
        $subscription->setUrl($url);
        $subscription->setSecret($secret);

        // Преобразуем строковые типы в UpdateType enum
        $enumTypes = [];
        foreach ($updateTypes as $type) {
            $enumTypes[] = UpdateType::from($type);
        }
        $subscription->setUpdateTypes($enumTypes);
        $subscription->setVersion('1.0.0');

        $this->client->subscribe($subscription);

        $this->logger->info("Webhook зарегистрирован: url={$url}, types=" . implode(',', $updateTypes));
    }
}
