<?php

declare(strict_types=1);

namespace MkdBot\Infrastructure\TelegramBot;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use MkdBot\Domain\Exception\ApiRateLimitExceededException;
use MkdBot\Domain\Interface\TelegramBotClientInterface;
use MkdBot\Infrastructure\Interface\TelegramWebhookClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\HttpClients\GuzzleHttpClient;

/**
 * Адаптер клиента Telegram Bot API — обёртка для irazasyed/telegram-bot-sdk
 * Реализует Domain-интерфейс (отправка сообщений) и Infrastructure-интерфейс (webhook)
 *
 * Rate limiting: 1 повторная попытка при 429 с задержкой из Retry-After
 * Обрезка текста: sendMessage — до 4096, sendPhoto caption — до 1024
 * Поддержка HTTP-прокси: если задан $httpProxy, все запросы к Telegram API идут через прокси
 * Поддержка аутентификации прокси: если заданы $httpProxyLogin и $httpProxyPassword,
 * прокси URL форматируется как http://login:password@host:port
 */
class TelegramBotClient implements TelegramBotClientInterface, TelegramWebhookClientInterface
{
    private const MAX_TEXT_LENGTH = 4096;
    private const MAX_CAPTION_LENGTH = 1024;
    private const TRUNCATE_SUFFIX = "\n...текст сокращён";

    private Api $api;
    private GuzzleClient $guzzle;

    public function __construct(
        string $token,
        private readonly LoggerInterface $logger,
        string $httpProxy = '',
        string $httpProxyLogin = '',
        string $httpProxyPassword = '',
    ) {
        // Создаём Guzzle-клиент с прокси для Telegram API (если прокси задан)
        $guzzleConfig = [];
        if ($httpProxy !== '') {
            $proxyUrl = $this->formatProxyUrl($httpProxy, $httpProxyLogin, $httpProxyPassword);
            $guzzleConfig['proxy'] = $proxyUrl;
            $this->logger->debug("Telegram Bot: используется HTTP-прокси {$httpProxy}");
        }

        $guzzleClient = new GuzzleClient($guzzleConfig);

        // Передаём Guzzle-клиент через GuzzleHttpClient в Api — все запросы к Telegram
        // будут идти через прокси (если он настроен)
        $httpClientHandler = new GuzzleHttpClient($guzzleClient);
        $this->api = new Api($token, false, $httpClientHandler);

        // Отдельный Guzzle-клиент без прокси — для скачивания документов по URL
        // (документы могут лежать на доступных хостах, прокси не нужен)
        $this->guzzle = new GuzzleClient();
    }

    public function sendMessage(array $params): mixed
    {
        if (isset($params['text']) && mb_strlen($params['text']) > self::MAX_TEXT_LENGTH) {
            $params['text'] = $this->truncateText($params['text'], self::MAX_TEXT_LENGTH);
        }

        return $this->requestWithRetry(fn () => $this->api->sendMessage($params));
    }

    public function sendPhoto(array $params): mixed
    {
        if (isset($params['caption']) && mb_strlen($params['caption']) > self::MAX_CAPTION_LENGTH) {
            $params['caption'] = $this->truncateText($params['caption'], self::MAX_CAPTION_LENGTH);
        }

        return $this->requestWithRetry(fn () => $this->api->sendPhoto($params));
    }

    public function sendDocument(array $params): mixed
    {
        if (isset($params['caption']) && mb_strlen($params['caption']) > self::MAX_CAPTION_LENGTH) {
            $params['caption'] = $this->truncateText($params['caption'], self::MAX_CAPTION_LENGTH);
        }

        // Если document — это URL, скачиваем во временный файл и отправляем через InputFile
        // sendDocument всегда вызывает uploadFile() — URL нужно скачивать, в отличие от sendPhoto
        if (isset($params['document']) && is_string($params['document']) && filter_var($params['document'], FILTER_VALIDATE_URL) !== false) {
            $tempPath = $this->downloadToTempFile($params['document']);

            if ($tempPath !== null) {
                $filename = $params['filename'] ?? basename(parse_url($params['document'], PHP_URL_PATH));
                $params['document'] = InputFile::create($tempPath, $filename);
                unset($params['filename']);

                try {
                    return $this->requestWithRetry(fn () => $this->api->sendDocument($params));
                } finally {
                    @unlink($tempPath);
                }
            }

            // Если скачивание не удалось — fallback: отправляем как текстовое уведомление
            $this->logger->warning("Не удалось скачать документ: " . $params['document']);

            return $this->sendMessage([
                'chat_id' => $params['chat_id'],
                'text' => "📎 Документ: недоступен для загрузки",
            ]);
        }

        return $this->requestWithRetry(fn () => $this->api->sendDocument($params));
    }

    public function setWebhook(array $params): bool
    {
        return $this->api->setWebhook($params);
    }

    public function getWebhookUpdate(?RequestInterface $request = null): mixed
    {
        return $this->api->getWebhookUpdate(false, $request);
    }

    public function getUpdates(array $params = []): array
    {
        return $this->api->getUpdates($params, false);
    }

    public function deleteWebhook(): bool
    {
        return $this->api->deleteWebhook();
    }

    /**
     * Форматирует URL прокси с учётом аутентификации
     * Если заданы login и password — возвращает http://login:password@host:port
     * Если login/password пустые — возвращает исходный http://host:port
     */
    private function formatProxyUrl(string $httpProxy, string $login, string $password): string
    {
        if ($login !== '' && $password !== '') {
            // Извлекаем host:port из прокси URL (убираем http:// префикс если есть)
            $hostPort = preg_replace('#^https?://#', '', $httpProxy);
            return "http://{$login}:{$password}@{$hostPort}";
        }

        return $httpProxy;
    }

    /**
     * Выполняет запрос с 1 повторной попыткой при 429 Too Many Requests
     * Задержка из Retry-After (заголовок или тело ответа) или 2 сек по умолчанию
     *
     * @throws TelegramResponseException При повторной неудаче 429 или других ошибках
     */
    private function requestWithRetry(callable $request): mixed
    {
        try {
            return $request();
        } catch (TelegramResponseException $e) {
            // Строгое сравнение: getHttpStatusCode() может вернуть null
            if ($e->getHttpStatusCode() === 429) {
                $retryAfter = $this->extractRetryAfter($e);
                $this->logger->warning("Telegram API: 429 Too Many Requests, Retry-After: {$retryAfter} сек");
                sleep($retryAfter);

                try {
                    return $request(); // 1 повторная попытка
                } catch (TelegramResponseException $retryEx) {
                    if ($retryEx->getHttpStatusCode() === 429) {
                        throw new ApiRateLimitExceededException('Telegram API: превышен лимит запросов после повторной попытки');
                    }
                    throw $retryEx;
                }
            }

            // Не-429 ошибки — пробрасываем без обработки
            throw $e;
        }
    }

    /**
     * Извлекает Retry-After из заголовков или тела ответа
     * Приоритет: заголовок, затем тело ответа, затем 2 сек по умолчанию
     */
    private function extractRetryAfter(TelegramResponseException $e): int
    {
        $response = $e->getResponse();

        if ($response !== null) { // @phpstan-ignore notIdentical.alwaysTrue — перестраховка при null-response
            // PSR-7 заголовки case-insensitive — приводим к нижнему регистру
            $headers = array_change_key_case($response->getHeaders(), CASE_LOWER);

            if (isset($headers['retry-after'][0])) {
                return (int)$headers['retry-after'][0];
            }

            // Тело ответа: parameters.retry_after
            $body = $response->getDecodedBody();

            if (isset($body['parameters']['retry_after'])) {
                return (int)$body['parameters']['retry_after'];
            }
        }

        return 2;
    }

    /**
     * Скачивает файл по URL во временный файл
     * Возвращает путь к временному файлу или null при ошибке
     */
    private function downloadToTempFile(string $url): ?string
    {
        try {
            $tempDir = sys_get_temp_dir() . '/mkd-bot';

            if (!is_dir($tempDir) && !mkdir($tempDir, 0755, true) && !is_dir($tempDir)) {
                throw new RuntimeException("Не удалось создать директорию: {$tempDir}");
            }

            $tempPath = $tempDir . '/' . uniqid('doc_', true);

            $this->guzzle->get($url, [
                'sink' => $tempPath,
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);

            if (!file_exists($tempPath) || filesize($tempPath) === 0) {
                @unlink($tempPath);
                return null;
            }

            return $tempPath;
        } catch (GuzzleException $e) {
            $this->logger->error("Ошибка скачивания документа: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Обрезает текст до указанного лимита с пометкой «...текст сокращён»
     */
    private function truncateText(string $text, int $maxLength): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $suffixLen = mb_strlen(self::TRUNCATE_SUFFIX);

        return mb_substr($text, 0, $maxLength - $suffixLen) . self::TRUNCATE_SUFFIX;
    }
}
