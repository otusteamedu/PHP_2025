<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Mailpit;

use App\Infrastructure\Mail\Exception\MailpitChaosException;
use App\Infrastructure\Mail\Smtp\SmtpCode;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MailpitChaosClient
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $baseUrl,
    ) {
    }

    /**
     * @throws MailpitChaosException
     */
    public function setChaos(MailpitChaosTrigger $trigger, SmtpCode $code, int $probability): void
    {
        $endpoint = $this->baseUrl . '/api/v1/chaos';

        $options = [
            'json' => [
                $trigger->value => [
                    'ErrorCode' => $code->value,
                    'Probability' => $probability,
                ],
            ],
        ];

        try {
            $response = $this->client->request('PUT', $endpoint, $options);

            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                throw MailpitChaosException::unexpectedStatus($statusCode);
            }

            $data = $response->toArray();

            $actual = $data[$trigger->value] ?? null;
            if ($actual === null) {
                throw MailpitChaosException::missingTrigger($trigger);
            }

            $settingsApplied = ($actual['ErrorCode'] ?? null) === $code->value
                && ($actual['Probability'] ?? null) === $probability;

            if (!$settingsApplied) {
                throw MailpitChaosException::settingsNotApplied($trigger, $code, $probability, $actual);
            }

        } catch (MailpitChaosException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw MailpitChaosException::configurationFailed($e);
        }
    }
}
