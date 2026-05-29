<?php

declare(strict_types=1);

namespace MkdBot\Presentation\Controller;

use MaxMessenger\Bot\Exceptions\MaxBot\Update\BadRequestException;
use MaxMessenger\Bot\MaxBot;
use MkdBot\Application\DTO\MaxCallbackDTO;
use MkdBot\Application\DTO\MaxMessageDTO;
use MkdBot\Application\UseCase\HandleMaxWebhook;
use MkdBot\Domain\Interface\ProcessedWebhookRepositoryInterface;
use MkdBot\Presentation\Mapper\MaxUpdateMapper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Контроллер webhook Max — endpoint /webhook/max
 * Получает тело запроса, парсит через MaxBot::makeUpdateFromString(),
 * маппит в Domain DTO, проверяет идемпотентность, передаёт в HandleMaxWebhook
 */
class MaxWebhookController
{
    public function __construct(
        private readonly MaxUpdateMapper $mapper,
        private readonly HandleMaxWebhook $handleMaxWebhook,
        private readonly ProcessedWebhookRepositoryInterface $processedWebhookRepo,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Обрабатывает входящий webhook от Max API
     */
    public function handle(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body = $request->getBody()->getContents();

        if (empty($body)) {
            $this->logger->warning("Пустое тело webhook от Max");
            return $response->withStatus(400);
        }

        try {
            // Парсим Update из тела запроса (не handleFromGlobal — он читает php://input)
            $update = MaxBot::makeUpdateFromString($body);
        } catch (BadRequestException $e) {
            $this->logger->error("Ошибка парсинга webhook Max: " . $e->getMessage());
            return $response->withStatus(400);
        } catch (Throwable $e) {
            // RequiredFieldException и другие ошибки валидации — тоже 400
            $this->logger->error("Ошибка валидации webhook Max: " . $e->getMessage());
            return $response->withStatus(400);
        }

        // Маппим Update в Domain DTO
        $dto = $this->mapper->map($update);

        if ($dto === null) {
            // Неизвестный/неподдерживаемый тип — подтверждаем получение (200 OK)
            return $response->withStatus(200);
        }

        // Идемпотентность: атомарная проверка+запись ДО обработки (tryAcquire)
        // Для bot_started/bot_stopped идемпотентность НЕ проверяется
        // Для callback с пустым callbackId идемпотентность тоже НЕ проверяется (аналогично bot_started)
        $mid = null;
        $checkIdempotency = false;

        if ($dto instanceof MaxMessageDTO) {
            $mid = $dto->mid;
            $checkIdempotency = true;
            $this->logger->info("Получен webhook Max: mid={$mid}, chatType={$dto->chatType}, chatId={$dto->chatId}");
        } elseif ($dto instanceof MaxCallbackDTO) {
            $mid = 'callback_' . $dto->callbackId;
            // Если callbackId пустой — пропускаем проверку идемпотентности (как bot_started)
            if ($dto->callbackId === null || $dto->callbackId === '') {
                $this->logger->debug("callbackId пустой, идемпотентность пропущена");
                $checkIdempotency = false;
            } else {
                $checkIdempotency = true;
            }
            $this->logger->info("Получен callback Max: callbackId={$dto->callbackId}, payload=" . json_encode($dto->payload, JSON_UNESCAPED_UNICODE));
        }

        if ($checkIdempotency && $mid !== null && $mid !== '') {
            if (!$this->processedWebhookRepo->tryAcquire($mid, 'max')) {
                $this->logger->debug("Webhook уже обработан: mid={$mid}");
                return $response->withStatus(200);
            }
        }

        // Передаём DTO в Use Case
        try {
            $this->handleMaxWebhook->execute($dto);
        } catch (Throwable $e) {
            $this->logger->error("Ошибка обработки webhook Max: " . $e->getMessage());
            // tryAcquire уже записал webhook — повторной обработки не будет
            return $response->withStatus(500);
        }

        return $response->withStatus(200);
    }
}
