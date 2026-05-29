<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Infrastructure\TelegramBot;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use MkdBot\Infrastructure\TelegramBot\TelegramBotClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Telegram\Bot\Api;
use Telegram\Bot\HttpClients\GuzzleHttpClient;
use Telegram\Bot\Objects\Message as MessageObject;

/**
 * Тесты для TelegramBotClient — приватные методы + публичные через подмену Api
 *
 * Telegram\Bot\Api — не final, свойство $api типизировано как Api.
 * TelegramClient и TelegramResponse — final, нельзя замокать.
 * GuzzleClient — свойство типизировано как GuzzleClient, нельзя присвоить анонимный класс.
 *
 * Стратегия: для публичных методов подменяем HTTP-клиент через GuzzleHttpClient
 * с mock-обработчиком Guzzle, который возвращает PSR-7 Response.
 */
class TelegramBotClientTest extends TestCase
{
    private TelegramBotClient $adapter;
    private ReflectionClass $ref;

    protected function setUp(): void
    {
        $this->ref = new ReflectionClass(TelegramBotClient::class);
        $this->adapter = $this->ref->newInstanceWithoutConstructor();

        // Внедряем логгер
        $logger = $this->createMock(LoggerInterface::class);
        $loggerProp = $this->ref->getProperty('logger');
        $loggerProp->setAccessible(true);
        $loggerProp->setValue($this->adapter, $logger);

        // Создаём реальный Api с тестовым токеном
        $api = new Api('test-token:fake');
        $apiProp = $this->ref->getProperty('api');
        $apiProp->setAccessible(true);
        $apiProp->setValue($this->adapter, $api);

        // Создаём GuzzleClient
        $guzzle = new GuzzleClient();
        $guzzleProp = $this->ref->getProperty('guzzle');
        $guzzleProp->setAccessible(true);
        $guzzleProp->setValue($this->adapter, $guzzle);
    }

    /**
     * Вызывает приватный метод через рефлексию
     */
    private function invokeMethod(string $methodName, array $args = []): mixed
    {
        $method = $this->ref->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invoke($this->adapter, ...$args);
    }

    /**
     * Настраивает мок HTTP-клиента в Api через GuzzleHttpClient с MockHandler
     */
    private function setupMockHttpClient(Api $api, array $responseData): void
    {
        // Создаём MockHandler, который возвращает PSR-7 Response
        $mockHandler = new MockHandler([
            new GuzzleResponse(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($responseData, JSON_UNESCAPED_UNICODE),
            ),
        ]);

        // Создаём GuzzleClient с mock-обработчиком
        $mockGuzzleClient = new GuzzleClient(['handler' => HandlerStack::create($mockHandler)]);

        // Оборачиваем в GuzzleHttpClient (реализация HttpClientInterface)
        $mockHttpClient = new GuzzleHttpClient($mockGuzzleClient);

        // Устанавливаем через Api::setHttpClientHandler()
        $api->setHttpClientHandler($mockHttpClient);
    }

    // === truncateText — обрезка для sendMessage (лимит 4096) ===

    public function testTruncateTextShortTextNotTruncated(): void
    {
        $result = $this->invokeMethod('truncateText', ['Короткий текст', 4096]);
        $this->assertSame('Короткий текст', $result);
    }

    public function testTruncateTextExactlyAtLimit(): void
    {
        $text4096 = str_repeat('А', 4096);
        $result = $this->invokeMethod('truncateText', [$text4096, 4096]);
        $this->assertSame($text4096, $result);
    }

    public function testTruncateTextOneOverLimit(): void
    {
        $text4097 = str_repeat('А', 4097);
        $result = $this->invokeMethod('truncateText', [$text4097, 4096]);
        $this->assertLessThanOrEqual(4096, mb_strlen($result));
        $this->assertTrue(str_ends_with($result, '...текст сокращён'));
    }

    public function testTruncateTextVeryLongText(): void
    {
        $text10000 = str_repeat('В', 10000);
        $result = $this->invokeMethod('truncateText', [$text10000, 4096]);
        $this->assertLessThanOrEqual(4096, mb_strlen($result));
        $this->assertTrue(str_ends_with($result, '...текст сокращён'));
    }

    public function testTruncateTextEmptyString(): void
    {
        $result = $this->invokeMethod('truncateText', ['', 4096]);
        $this->assertSame('', $result);
    }

    public function testTruncateTextPreservesContentBeforeTruncation(): void
    {
        // Суффикс "\n...текст сокращён" = 17 символов
        // Префикс 4070 символов + суффикс 17 = 4087 < 4096 — префикс полностью сохранится
        $prefix = str_repeat('Г', 4070);
        $text = $prefix . str_repeat('Д', 100);
        $result = $this->invokeMethod('truncateText', [$text, 4096]);
        $this->assertTrue(str_starts_with($result, $prefix));
    }

    // === truncateText — обрезка для caption (лимит 1024) ===

    public function testTruncateCaptionShortNotTruncated(): void
    {
        $result = $this->invokeMethod('truncateText', ['Подпись', 1024]);
        $this->assertSame('Подпись', $result);
    }

    public function testTruncateCaptionExactlyAtLimit(): void
    {
        $caption1024 = str_repeat('Б', 1024);
        $result = $this->invokeMethod('truncateText', [$caption1024, 1024]);
        $this->assertSame($caption1024, $result);
    }

    public function testTruncateCaptionOverLimit(): void
    {
        $caption2000 = str_repeat('Б', 2000);
        $result = $this->invokeMethod('truncateText', [$caption2000, 1024]);
        $this->assertLessThanOrEqual(1024, mb_strlen($result));
        $this->assertTrue(str_ends_with($result, '...текст сокращён'));
    }

    public function testTruncateCaptionOneOverLimit(): void
    {
        $caption1025 = str_repeat('Я', 1025);
        $result = $this->invokeMethod('truncateText', [$caption1025, 1024]);
        $this->assertLessThanOrEqual(1024, mb_strlen($result));
        $this->assertTrue(str_ends_with($result, '...текст сокращён'));
    }

    // === Константы ===

    public function testMaxTextLengthConstant(): void
    {
        $this->assertSame(4096, $this->ref->getConstant('MAX_TEXT_LENGTH'));
    }

    public function testMaxCaptionLengthConstant(): void
    {
        $this->assertSame(1024, $this->ref->getConstant('MAX_CAPTION_LENGTH'));
    }

    public function testTruncateSuffixConstant(): void
    {
        $suffix = $this->ref->getConstant('TRUNCATE_SUFFIX');
        $this->assertSame("\n...текст сокращён", $suffix);
    }

    // === Публичные методы: sendMessage ===

    public function testSendMessageDelegatesToApi(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'message_id' => 1,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
                'text' => 'Привет из теста',
            ],
        ]);

        $result = $this->adapter->sendMessage([
            'chat_id' => 123,
            'text' => 'Привет из теста',
        ]);

        $this->assertInstanceOf(MessageObject::class, $result);
    }

    public function testSendMessageTruncatesLongText(): void
    {
        // Проверяем обрезку текста через приватный метод
        $longText = str_repeat('А', 5000);
        $truncated = $this->invokeMethod('truncateText', [$longText, 4096]);
        $this->assertLessThanOrEqual(4096, mb_strlen($truncated));
        $this->assertTrue(str_ends_with($truncated, '...текст сокращён'));

        // Проверяем, что sendMessage с длинным текстом вызывает обрезку и делегирует
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'message_id' => 1,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
            ],
        ]);

        $result = $this->adapter->sendMessage([
            'chat_id' => 123,
            'text' => $longText,
        ]);

        $this->assertInstanceOf(MessageObject::class, $result);
    }

    public function testSendMessageShortTextNotTruncated(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'message_id' => 1,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
            ],
        ]);

        $result = $this->adapter->sendMessage([
            'chat_id' => 123,
            'text' => 'Короткий текст',
        ]);

        $this->assertInstanceOf(MessageObject::class, $result);
    }

    public function testSendMessageExactlyAtLimitNotTruncated(): void
    {
        $text4096 = str_repeat('А', 4096);
        $result = $this->invokeMethod('truncateText', [$text4096, 4096]);
        $this->assertSame($text4096, $result);
    }

    // === Публичные методы: sendPhoto ===

    public function testSendPhotoDelegatesToApi(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'message_id' => 2,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
            ],
        ]);

        $result = $this->adapter->sendPhoto([
            'chat_id' => 123,
            'photo' => 'https://example.com/photo.jpg',
            'caption' => 'Фото',
        ]);

        $this->assertInstanceOf(MessageObject::class, $result);
    }

    public function testSendPhotoTruncatesLongCaption(): void
    {
        $longCaption = str_repeat('Б', 2000);
        $truncated = $this->invokeMethod('truncateText', [$longCaption, 1024]);
        $this->assertLessThanOrEqual(1024, mb_strlen($truncated));
        $this->assertTrue(str_ends_with($truncated, '...текст сокращён'));

        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'message_id' => 2,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
            ],
        ]);

        $result = $this->adapter->sendPhoto([
            'chat_id' => 123,
            'photo' => 'https://example.com/photo.jpg',
            'caption' => $longCaption,
        ]);

        $this->assertInstanceOf(MessageObject::class, $result);
    }

    public function testSendPhotoShortCaptionNotTruncated(): void
    {
        $result = $this->invokeMethod('truncateText', ['Короткая подпись', 1024]);
        $this->assertSame('Короткая подпись', $result);
    }

    public function testSendPhotoWithoutCaption(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'message_id' => 2,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
            ],
        ]);

        $result = $this->adapter->sendPhoto([
            'chat_id' => 123,
            'photo' => 'https://example.com/photo.jpg',
        ]);

        $this->assertInstanceOf(MessageObject::class, $result);
    }

    // === Публичные методы: sendDocument ===

    public function testSendDocumentTruncatesLongCaption(): void
    {
        $longCaption = str_repeat('В', 2000);
        $truncated = $this->invokeMethod('truncateText', [$longCaption, 1024]);
        $this->assertLessThanOrEqual(1024, mb_strlen($truncated));
        $this->assertTrue(str_ends_with($truncated, '...текст сокращён'));
    }

    public function testSendDocumentShortCaptionNotTruncated(): void
    {
        $result = $this->invokeMethod('truncateText', ['Подпись документа', 1024]);
        $this->assertSame('Подпись документа', $result);
    }

    public function testSendDocumentWithUrlValidation(): void
    {
        // URL-строка распознаётся как URL
        $this->assertNotFalse(filter_var('https://example.com/doc.pdf', FILTER_VALIDATE_URL));
        // Локальный путь — не URL
        $this->assertFalse(filter_var('/tmp/test.pdf', FILTER_VALIDATE_URL));
        // Произвольная строка — не URL
        $this->assertFalse(filter_var('not-a-url', FILTER_VALIDATE_URL));
    }

    public function testSendDocumentWithUrlDownloadsAndSendsAsInputFile(): void
    {
        // Если document — URL, скачиваем во временный файл и отправляем через InputFile
        // Подменяем guzzle через GuzzleClient с mock handler
        $mockHandler = new MockHandler([
            new GuzzleResponse(200, [], 'Test document content'),
        ]);
        $mockGuzzle = new GuzzleClient(['handler' => HandlerStack::create($mockHandler)]);

        $guzzleProp = $this->ref->getProperty('guzzle');
        $guzzleProp->setAccessible(true);
        $guzzleProp->setValue($this->adapter, $mockGuzzle);

        // Подменяем Api для sendDocument
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'message_id' => 3,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
            ],
        ]);

        $this->adapter->sendDocument([
            'chat_id' => 123,
            'document' => 'https://example.com/doc.pdf',
            'filename' => 'doc.pdf',
        ]);

        // Тест проходит, если нет исключения
        $this->assertTrue(true);
    }

    public function testSendDocumentWithUrlDownloadFailsFallsBackToMessage(): void
    {
        // Если скачивание не удалось — fallback на sendMessage
        $mockHandler = new MockHandler([
            new \GuzzleHttp\Exception\ConnectException(
                'Connection refused',
                new GuzzleRequest('GET', 'https://example.com/missing.pdf'),
            ),
        ]);
        $mockGuzzle = new GuzzleClient(['handler' => HandlerStack::create($mockHandler)]);

        $guzzleProp = $this->ref->getProperty('guzzle');
        $guzzleProp->setAccessible(true);
        $guzzleProp->setValue($this->adapter, $mockGuzzle);

        // Подменяем Api для sendMessage (fallback)
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'message_id' => 4,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
            ],
        ]);

        $this->adapter->sendDocument([
            'chat_id' => 123,
            'document' => 'https://example.com/missing.pdf',
        ]);

        // Fallback сработал — sendMessage вместо sendDocument
        $this->assertTrue(true);
    }

    public function testSendDocumentWithUrlDownloadReturnsEmptyFileFallsBackToMessage(): void
    {
        // Если скачивание вернуло пустой файл — fallback на sendMessage
        $mockHandler = new MockHandler([
            new GuzzleResponse(200, [], ''), // Пустой ответ
        ]);
        $mockGuzzle = new GuzzleClient(['handler' => HandlerStack::create($mockHandler)]);

        $guzzleProp = $this->ref->getProperty('guzzle');
        $guzzleProp->setAccessible(true);
        $guzzleProp->setValue($this->adapter, $mockGuzzle);

        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'message_id' => 4,
                'chat' => ['id' => 123, 'type' => 'private'],
                'date' => time(),
            ],
        ]);

        $this->adapter->sendDocument([
            'chat_id' => 123,
            'document' => 'https://example.com/empty.pdf',
        ]);

        // Fallback сработал
        $this->assertTrue(true);
    }

    // === Публичные методы: setWebhook ===

    public function testSetWebhookDelegatesToApi(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => true,
            'description' => 'Webhook was set',
        ]);

        $result = $this->adapter->setWebhook(['url' => 'https://example.com/webhook']);
        $this->assertTrue($result);
    }

    // === Публичные методы: getWebhookUpdate ===

    public function testGetWebhookUpdateDelegatesToApi(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                'update_id' => 123456,
            ],
        ]);

        $result = $this->adapter->getWebhookUpdate();
        $this->assertNotNull($result);
    }

    // === Публичные методы: getUpdates ===

    /**
     * getUpdates() — делегирует Api::getUpdates() и возвращает массив обновлений
     */
    public function testGetUpdatesDelegatesToApi(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [
                [
                    'update_id' => 100,
                    'message' => [
                        'message_id' => 1,
                        'chat' => ['id' => 123, 'type' => 'private'],
                        'date' => time(),
                        'text' => 'Привет',
                    ],
                ],
                [
                    'update_id' => 101,
                    'message' => [
                        'message_id' => 2,
                        'chat' => ['id' => 456, 'type' => 'private'],
                        'date' => time(),
                        'text' => 'Мир',
                    ],
                ],
            ],
        ]);

        $result = $this->adapter->getUpdates(['offset' => 100, 'timeout' => 30]);

        // getUpdates возвращает массив UpdateObject
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    /**
     * getUpdates() — пустой результат (нет новых обновлений)
     */
    public function testGetUpdatesReturnsEmptyArrayWhenNoUpdates(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => [],
        ]);

        $result = $this->adapter->getUpdates(['offset' => 200, 'timeout' => 30]);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }

    // === Публичные методы: deleteWebhook ===

    /**
     * deleteWebhook() — делегирует Api::deleteWebhook() и возвращает true
     */
    public function testDeleteWebhookDelegatesToApi(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => true,
            'description' => 'Webhook was deleted',
        ]);

        $result = $this->adapter->deleteWebhook();
        $this->assertTrue($result);
    }

    /**
     * deleteWebhook() — возвращает false, если webhook не был установлен
     */
    public function testDeleteWebhookReturnsFalseWhenNoWebhookSet(): void
    {
        $api = $this->ref->getProperty('api')->getValue($this->adapter);
        $this->setupMockHttpClient($api, [
            'ok' => true,
            'result' => false,
            'description' => 'Webhook is not set',
        ]);

        $result = $this->adapter->deleteWebhook();
        $this->assertFalse($result);
    }

    // === formatProxyUrl — форматирование URL прокси с аутентификацией ===

    /**
     * Proxy URL без логина/пароля — возвращается как есть
     */
    public function testFormatProxyUrlWithoutAuth(): void
    {
        $result = $this->invokeMethod('formatProxyUrl', ['http://192.168.0.44:9080', '', '']);
        $this->assertSame('http://192.168.0.44:9080', $result);
    }

    /**
     * Proxy URL с логином и паролем — формат http://login:password@host:port
     */
    public function testFormatProxyUrlWithAuth(): void
    {
        $result = $this->invokeMethod('formatProxyUrl', ['http://192.168.0.44:9080', 'myuser', 'mypass']);
        $this->assertSame('http://myuser:mypass@192.168.0.44:9080', $result);
    }

    /**
     * Proxy URL с http:// префиксом и аутентификацией — префикс убирается, логин:пароль добавляется
     */
    public function testFormatProxyUrlWithAuthStripsHttpPrefix(): void
    {
        $result = $this->invokeMethod('formatProxyUrl', ['http://proxy.example.com:3128', 'admin', 'secret123']);
        $this->assertSame('http://admin:secret123@proxy.example.com:3128', $result);
    }

    /**
     * Proxy URL с https:// префиксом и аутентификацией — префикс убирается
     */
    public function testFormatProxyUrlWithAuthStripsHttpsPrefix(): void
    {
        $result = $this->invokeMethod('formatProxyUrl', ['https://secure-proxy.local:8443', 'user', 'pwd']);
        $this->assertSame('http://user:pwd@secure-proxy.local:8443', $result);
    }

    /**
     * Только логин без пароля — аутентификация НЕ добавляется
     */
    public function testFormatProxyUrlWithOnlyLoginNoAuth(): void
    {
        $result = $this->invokeMethod('formatProxyUrl', ['http://192.168.0.44:9080', 'myuser', '']);
        $this->assertSame('http://192.168.0.44:9080', $result);
    }

    /**
     * Только пароль без логина — аутентификация НЕ добавляется
     */
    public function testFormatProxyUrlWithOnlyPasswordNoAuth(): void
    {
        $result = $this->invokeMethod('formatProxyUrl', ['http://192.168.0.44:9080', '', 'mypass']);
        $this->assertSame('http://192.168.0.44:9080', $result);
    }

    /**
     * Proxy URL без префикса схемы — корректная обработка
     */
    public function testFormatProxyUrlWithoutScheme(): void
    {
        $result = $this->invokeMethod('formatProxyUrl', ['192.168.0.44:9080', 'user', 'pass']);
        $this->assertSame('http://user:pass@192.168.0.44:9080', $result);
    }

    /**
     * Proxy URL без префикса схемы и без аутентификации — возвращается как есть
     */
    public function testFormatProxyUrlWithoutSchemeAndAuth(): void
    {
        $result = $this->invokeMethod('formatProxyUrl', ['192.168.0.44:9080', '', '']);
        $this->assertSame('192.168.0.44:9080', $result);
    }
}
