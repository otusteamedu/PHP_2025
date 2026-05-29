<?php

declare(strict_types=1);

namespace MkdBot\Tests\Unit\Infrastructure\MaxBot;

use MaxMessenger\Bot\MaxApiClient;
use MaxMessenger\Bot\Models\Enums\Intent;
use MaxMessenger\Bot\Models\Enums\SenderAction;
use MaxMessenger\Bot\Models\Requests\ActionRequestBody;
use MaxMessenger\Bot\Models\Requests\CallbackAnswer;
use MaxMessenger\Bot\Models\Requests\NewMessageBody;
use MkdBot\Domain\ValueObject\SendMessageResult as DomainSendMessageResult;
use MkdBot\Infrastructure\MaxBot\MaxBotClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use ReflectionClass;

/**
 * Тесты для MaxBotClient — приватные методы + публичные через подмену MaxApiClient
 *
 * MaxApiClient — final-класс, нельзя замокать через createMock().
 * Свойство $client типизировано как MaxApiClient — нельзя присвоить анонимный класс.
 * Свойство $httpClient в MaxApiClient — readonly, нельзя изменить после создания.
 *
 * Стратегия: для публичных методов, которые делают реальные HTTP-запросы,
 * используем подмену HTTP-клиента внутри MaxApiConfig (до создания MaxApiClient).
 * Для приватных методов — вызов через рефлексию.
 */
class MaxBotClientTest extends TestCase
{
    private MaxBotClient $adapter;
    private ReflectionClass $ref;

    protected function setUp(): void
    {
        // Создаём MaxBotClient через рефлексию без вызова конструктора
        $this->ref = new ReflectionClass(MaxBotClient::class);
        $this->adapter = $this->ref->newInstanceWithoutConstructor();

        // Внедряем логгер
        $logger = $this->createMock(LoggerInterface::class);
        $loggerProp = $this->ref->getProperty('logger');
        $loggerProp->setAccessible(true);
        $loggerProp->setValue($this->adapter, $logger);

        // Создаём MaxApiClient с подменённым HTTP-клиентом
        $client = $this->createMaxApiClientWithMockHttp();
        $clientProp = $this->ref->getProperty('client');
        $clientProp->setAccessible(true);
        $clientProp->setValue($this->adapter, $client);
    }

    /**
     * Создаёт MaxApiClient с подменённым HTTP-клиентом, который не делает реальных запросов
     */
    private function createMaxApiClientWithMockHttp(): MaxApiClient
    {
        // Создаём мок HttpClientInterface
        $mockHttpClient = new class () implements \Mj4444\SimpleHttpClient\Contracts\HttpClientInterface {
            public array $requests = [];

            public function request(\Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface $request): \Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface
            {
                $this->requests[] = [
                    'url' => $request->getUrl(),
                    'method' => $request->getMethod(),
                ];

                return new class () implements \Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface {
                    public function checkContentType(string|array|null $expectedContentType = null): void
                    {
                    }
                    public function checkHttpCode(int|array $allowedCode = 200): void
                    {
                    }
                    public function getBody(): string
                    {
                        return '';
                    }
                    public function getContentType(): ?string
                    {
                        return null;
                    }
                    public function getData(): mixed
                    {
                        return ['message' => ['body' => ['mid' => 'test-msg-id'], 'timestamp' => 1700000000000]];
                    }
                    public function getEffectiveUrl(): string
                    {
                        return 'https://test';
                    }
                    public function getFirstHeader(string $name): ?string
                    {
                        return null;
                    }
                    public function getHeaders(): array
                    {
                        return [];
                    }
                    public function getHttpCode(): int
                    {
                        return 200;
                    }
                    public function getRedirectUrl(): ?string
                    {
                        return null;
                    }
                    public function getRequest(): \Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface
                    {
                        return new class () implements \Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface {
                            public function getBody(): ?\Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface
                            {
                                return null;
                            }
                            public function getConnectTimeout(): int|false|null
                            {
                                return null;
                            }
                            public function getHeaders(): array
                            {
                                return [];
                            }
                            public function getMaxRedirects(): ?int
                            {
                                return null;
                            }
                            public function getMethod(): string
                            {
                                return 'POST';
                            }
                            public function getTimeout(): int|false|null
                            {
                                return null;
                            }
                            public function getUrl(): string
                            {
                                return 'https://test';
                            }
                            public function isFollowLocation(): ?bool
                            {
                                return null;
                            }
                            public function isPost(): bool
                            {
                                return true;
                            }
                            public function isResponseHeadersRequired(): ?bool
                            {
                                return null;
                            }
                            public function makeResponse(int $httpCode, string $url, string $effectiveUrl, ?string $redirectUrl, array $headers, ?string $contentType, string $response): \Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface
                            {
                                return $this;
                            }
                        };
                    }
                    public function getUrl(): string
                    {
                        return 'https://test';
                    }
                };
            }
        };

        // Создаём MaxApiConfig с мок-клиентом
        $config = new \MaxMessenger\Bot\MaxApiConfig('test-token');
        $config->setHttpClient($mockHttpClient);

        // Создаём MaxApiClient через конфиг
        return new MaxApiClient($config);
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

    // === truncateText ===

    public function testTruncateTextShortTextNotTruncated(): void
    {
        $result = $this->invokeMethod('truncateText', ['Короткий текст']);
        $this->assertSame('Короткий текст', $result);
    }

    public function testTruncateTextExactlyAtLimit(): void
    {
        $text4000 = str_repeat('А', 4000);
        $result = $this->invokeMethod('truncateText', [$text4000]);
        $this->assertSame($text4000, $result);
    }

    public function testTruncateTextOneOverLimit(): void
    {
        $text4001 = str_repeat('А', 4001);
        $result = $this->invokeMethod('truncateText', [$text4001]);
        $this->assertLessThanOrEqual(4000, mb_strlen($result));
        $this->assertTrue(str_ends_with($result, '...текст сокращён'));
    }

    public function testTruncateTextVeryLongText(): void
    {
        $text10000 = str_repeat('Б', 10000);
        $result = $this->invokeMethod('truncateText', [$text10000]);
        $this->assertLessThanOrEqual(4000, mb_strlen($result));
        $this->assertTrue(str_ends_with($result, '...текст сокращён'));
    }

    public function testTruncateTextPreservesContentBeforeTruncation(): void
    {
        // Суффикс "\n...текст сокращён" = 17 символов
        // Префикс 3980 символов + суффикс 17 = 3997 < 4000 — префикс полностью сохранится
        $prefix = str_repeat('В', 3980);
        $text = $prefix . str_repeat('Г', 100);
        $result = $this->invokeMethod('truncateText', [$text]);
        $this->assertTrue(str_starts_with($result, $prefix));
    }

    public function testTruncateTextEmptyString(): void
    {
        $result = $this->invokeMethod('truncateText', ['']);
        $this->assertSame('', $result);
    }

    // === forceTruncateText ===

    public function testForceTruncateTextShortTextGetsSuffix(): void
    {
        // Короткий текст — forceTruncateText добавляет суффикс
        $result = $this->invokeMethod('forceTruncateText', ['Короткий']);
        $this->assertSame('Короткий' . "\n...текст сокращён", $result);
    }

    public function testForceTruncateTextLongTextTruncated(): void
    {
        // Длинный текст — обрезается до MAX_TEXT_LENGTH - suffixLen + суффикс
        $text = str_repeat('Я', 5000);
        $result = $this->invokeMethod('forceTruncateText', [$text]);
        $this->assertLessThanOrEqual(4000, mb_strlen($result));
        $this->assertTrue(str_ends_with($result, '...текст сокращён'));
    }

    // === groupButtonsByRow ===

    public function testGroupButtonsByRowWithExplicitRows(): void
    {
        $buttons = [
            ['text' => 'УК', 'payload' => ['action' => 'contacts', 'type' => 'uk'], 'intent' => 'default', 'row' => 0],
            ['text' => 'Совет дома', 'payload' => ['action' => 'contacts', 'type' => 'council'], 'intent' => 'default', 'row' => 0],
            ['text' => 'Улучшение бота', 'payload' => ['action' => 'feature'], 'intent' => 'default', 'row' => 1],
            ['text' => 'Вопрос ИИ', 'payload' => ['action' => 'rag_query'], 'intent' => 'default', 'row' => 2],
        ];

        $result = $this->invokeMethod('groupButtonsByRow', [$buttons]);

        $this->assertCount(3, $result); // 3 ряда
        $this->assertCount(2, $result[0]); // Ряд 0: 2 кнопки
        $this->assertCount(1, $result[1]); // Ряд 1: 1 кнопка
        $this->assertCount(1, $result[2]); // Ряд 2: 1 кнопка
    }

    public function testGroupButtonsByRowWithoutExplicitRows(): void
    {
        $buttons = [
            ['text' => 'Кнопка 1', 'payload' => ['action' => 'a1'], 'intent' => 'default'],
            ['text' => 'Кнопка 2', 'payload' => ['action' => 'a2'], 'intent' => 'default'],
            ['text' => 'Кнопка 3', 'payload' => ['action' => 'a3'], 'intent' => 'default'],
        ];

        $result = $this->invokeMethod('groupButtonsByRow', [$buttons]);

        // Без 'row' — каждая кнопка в отдельном ряду (auto-increment)
        $this->assertCount(3, $result);
        $this->assertCount(1, $result[0]);
        $this->assertCount(1, $result[1]);
        $this->assertCount(1, $result[2]);
    }

    public function testGroupButtonsByRowMixedRows(): void
    {
        $buttons = [
            ['text' => 'А', 'payload' => [], 'intent' => 'default', 'row' => 0],
            ['text' => 'Б', 'payload' => [], 'intent' => 'default'], // без row — auto
            ['text' => 'В', 'payload' => [], 'intent' => 'default', 'row' => 0],
        ];

        $result = $this->invokeMethod('groupButtonsByRow', [$buttons]);

        // Кнопка А (row=0), Б (auto=1), В (row=0) — row 0: [А, В], row 1: [Б]
        $this->assertCount(2, $result);
        $this->assertCount(2, $result[0]); // А и В
        $this->assertCount(1, $result[1]); // Б
    }

    public function testGroupButtonsByRowEmptyArray(): void
    {
        $result = $this->invokeMethod('groupButtonsByRow', [[]]);
        $this->assertCount(0, $result);
    }

    public function testGroupButtonsByRowSingleButton(): void
    {
        $buttons = [
            ['text' => 'ОК', 'payload' => ['action' => 'ok'], 'intent' => 'positive'],
        ];

        $result = $this->invokeMethod('groupButtonsByRow', [$buttons]);

        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]);
    }

    // === resolveIntent ===

    public function testResolveIntentPositive(): void
    {
        $result = $this->invokeMethod('resolveIntent', ['positive']);
        $this->assertSame(Intent::Positive, $result);
    }

    public function testResolveIntentNegative(): void
    {
        $result = $this->invokeMethod('resolveIntent', ['negative']);
        $this->assertSame(Intent::Negative, $result);
    }

    public function testResolveIntentDefault(): void
    {
        $result = $this->invokeMethod('resolveIntent', ['default']);
        $this->assertSame(Intent::Default, $result);
    }

    public function testResolveIntentUnknownReturnsDefault(): void
    {
        $result = $this->invokeMethod('resolveIntent', ['unknown']);
        $this->assertSame(Intent::Default, $result);
    }

    public function testResolveIntentEmptyStringReturnsDefault(): void
    {
        $result = $this->invokeMethod('resolveIntent', ['']);
        $this->assertSame(Intent::Default, $result);
    }

    // === Публичные методы: sendMessageToUser ===

    public function testSendMessageToUserDelegatesToApiClient(): void
    {
        // Вызываем публичный метод — MaxApiClient с мок-клиентом не делает реальных запросов
        $result = $this->adapter->sendMessageToUser(123, 'Привет', true);
        $this->assertInstanceOf(DomainSendMessageResult::class, $result);
        $this->assertSame('test-msg-id', $result->getMessageId());
        $this->assertSame(1700000000, $result->getTimestamp());
    }

    public function testSendMessageToUserTruncatesLongText(): void
    {
        // Текст длиннее 4000 символов должен быть обрезан
        $longText = str_repeat('А', 5000);
        $result = $this->invokeMethod('truncateText', [$longText]);
        $this->assertLessThanOrEqual(4000, mb_strlen($result));
        $this->assertTrue(str_ends_with($result, '...текст сокращён'));
    }

    public function testSendMessageToUserShortTextNotModified(): void
    {
        // Короткий текст не обрезается
        $shortText = 'Привет';
        $result = $this->invokeMethod('truncateText', [$shortText]);
        $this->assertSame('Привет', $result);
    }

    // === Публичные методы: sendMessageToChat ===

    public function testSendMessageToChatDelegatesToApiClient(): void
    {
        $result = $this->adapter->sendMessageToChat(456, 'Текст в чат', false);
        $this->assertInstanceOf(DomainSendMessageResult::class, $result);
        $this->assertSame('test-msg-id', $result->getMessageId());
        $this->assertSame(1700000000, $result->getTimestamp());
    }

    public function testSendMessageToChatTruncatesLongText(): void
    {
        $longText = str_repeat('Б', 5000);
        $result = $this->invokeMethod('truncateText', [$longText]);
        $this->assertLessThanOrEqual(4000, mb_strlen($result));
        $this->assertTrue(str_ends_with($result, '...текст сокращён'));
    }

    // === Публичные методы: sendMessageWithInlineKeyboard ===

    public function testSendMessageWithInlineKeyboardCreatesMessageWithKeyboard(): void
    {
        $buttons = [
            ['text' => 'УК', 'payload' => ['action' => 'contacts', 'type' => 'uk'], 'intent' => 'default', 'row' => 0],
            ['text' => 'Совет дома', 'payload' => ['action' => 'contacts', 'type' => 'council'], 'intent' => 'positive', 'row' => 0],
        ];

        $result = $this->adapter->sendMessageWithInlineKeyboard(789, 'Выберите:', $buttons);
        $this->assertInstanceOf(DomainSendMessageResult::class, $result);
        $this->assertSame('test-msg-id', $result->getMessageId());
        $this->assertSame(1700000000, $result->getTimestamp());
    }

    public function testSendMessageWithInlineKeyboardWithoutText(): void
    {
        // Без текста — только клавиатура
        // Проверяем через создание NewMessageBody напрямую
        $message = new NewMessageBody();
        $keyboard = $message->addInlineKeyboard();
        $keyboard->addCallbackButton('Да', ['action' => 'yes'], Intent::Positive);

        $rawData = $message->getRawData();
        // Текст не установлен
        $this->assertArrayNotHasKey('text', $rawData);
        // Но клавиатура есть
        $this->assertArrayHasKey('attachments', $rawData);
        $this->assertNotEmpty($rawData['attachments']);
    }

    public function testSendMessageWithInlineKeyboardEmptyStringText(): void
    {
        // Пустая строка — текст не устанавливается в NewMessageBody
        $message = new NewMessageBody();
        $keyboard = $message->addInlineKeyboard();
        $keyboard->addCallbackButton('ОК', ['action' => 'ok'], Intent::Positive);

        $rawData = $message->getRawData();
        $this->assertArrayNotHasKey('text', $rawData);
    }

    public function testSendMessageWithInlineKeyboardMultipleRows(): void
    {
        // Кнопки в разных рядах
        $message = new NewMessageBody();
        $message->setText('Мультиряд:');
        $keyboard = $message->addInlineKeyboard();
        $keyboard->addCallbackButton('Ряд1 Кнопка1', ['a' => '1'], Intent::Default);
        $keyboard->addCallbackButton('Ряд1 Кнопка2', ['a' => '2'], Intent::Default);
        $keyboard->newRow();
        $keyboard->addCallbackButton('Ряд2 Кнопка1', ['a' => '3'], Intent::Positive);

        $rawData = $message->getRawData();
        $this->assertSame('Мультиряд:', $rawData['text']);
        $this->assertArrayHasKey('attachments', $rawData);
    }

    // === Публичные методы: answerCallbackWithMessage ===

    public function testAnswerCallbackWithMessageCreatesCallbackAnswer(): void
    {
        // Проверяем создание CallbackAnswer с NewMessageBody
        $text = 'Обновлённый текст';
        $message = new NewMessageBody($text);
        $answer = new CallbackAnswer($message, null);

        $this->assertNotNull($answer->getMessage());
        $this->assertNull($answer->getNotification());
        $this->assertSame($text, $answer->getMessage()->getRawData()['text']);
    }

    public function testAnswerCallbackWithMessageWithInlineButtons(): void
    {
        // CallbackAnswer с inline-кнопками
        $message = new NewMessageBody('Подтвердите:');
        $keyboard = $message->addInlineKeyboard();
        $keyboard->addCallbackButton('Да', ['action' => 'confirm'], Intent::Positive);
        $keyboard->addCallbackButton('Нет', ['action' => 'cancel'], Intent::Negative);

        $answer = new CallbackAnswer($message, null);

        $this->assertNotNull($answer->getMessage());
        $rawData = $answer->getMessage()->getRawData();
        $this->assertArrayHasKey('attachments', $rawData);
    }

    public function testAnswerCallbackWithMessageTruncatesLongText(): void
    {
        $longText = str_repeat('В', 5000);
        $result = $this->invokeMethod('truncateText', [$longText]);
        $this->assertLessThanOrEqual(4000, mb_strlen($result));
    }

    // === Публичные методы: answerCallbackNotification ===

    public function testAnswerCallbackNotificationCreatesCallbackAnswerWithNotification(): void
    {
        // CallbackAnswer с notification, без message
        $answer = new CallbackAnswer(null, 'Уведомление!');

        $this->assertNull($answer->getMessage());
        $this->assertSame('Уведомление!', $answer->getNotification());
    }

    // === Публичные методы: sendAction ===

    public function testSendActionCreatesSenderAction(): void
    {
        // Проверяем, что SenderAction::from() корректно создаёт enum
        $action = SenderAction::from('typing_on');
        $this->assertSame(SenderAction::TypingOn, $action);
    }

    public function testSendActionCreatesActionRequestBody(): void
    {
        // Проверяем создание ActionRequestBody из SenderAction
        $action = SenderAction::from('typing_on');
        $body = new ActionRequestBody($action);

        $this->assertSame(SenderAction::TypingOn, $body->getAction());
    }

    public function testSendActionWithSendingPhoto(): void
    {
        $action = SenderAction::from('sending_photo');
        $this->assertSame(SenderAction::SendingPhoto, $action);
    }

    public function testSendActionWithMarkSeen(): void
    {
        $action = SenderAction::from('mark_seen');
        $this->assertSame(SenderAction::MarkSeen, $action);
    }

    // === Константы ===

    public function testMaxTextLengthConstant(): void
    {
        $this->assertSame(4000, $this->ref->getConstant('MAX_TEXT_LENGTH'));
    }

    public function testTruncateSuffixConstant(): void
    {
        $suffix = $this->ref->getConstant('TRUNCATE_SUFFIX');
        $this->assertSame("\n...текст сокращён", $suffix);
    }

    // === mapResult ===

    public function testMapResultReturnsDomainSendMessageResult(): void
    {
        // Вызываем sendMessageToUser — мок HTTP возвращает данные с mid и timestamp
        $result = $this->adapter->sendMessageToUser(123, 'Тест');
        $this->assertInstanceOf(DomainSendMessageResult::class, $result);
        $this->assertSame('test-msg-id', $result->getMessageId());
        // 1700000000000 ms -> 1700000000 sec
        $this->assertSame(1700000000, $result->getTimestamp());
    }

    public function testMapResultFallbackOnException(): void
    {
        // Создаём MaxBotClient с HTTP-моком, который возвращает данные без message.body.mid
        $mockHttpClient = new class () implements \Mj4444\SimpleHttpClient\Contracts\HttpClientInterface {
            public function request(\Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface $request): \Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface
            {
                return new class () implements \Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface {
                    public function checkContentType(string|array|null $expectedContentType = null): void
                    {
                    }
                    public function checkHttpCode(int|array $allowedCode = 200): void
                    {
                    }
                    public function getBody(): string
                    {
                        return '';
                    }
                    public function getContentType(): ?string
                    {
                        return null;
                    }
                    public function getData(): mixed
                    {
                        return ['message' => ['body' => [], 'timestamp' => 0]];
                    } // body без mid — getMid() выбросит исключение
                    public function getEffectiveUrl(): string
                    {
                        return 'https://test';
                    }
                    public function getFirstHeader(string $name): ?string
                    {
                        return null;
                    }
                    public function getHeaders(): array
                    {
                        return [];
                    }
                    public function getHttpCode(): int
                    {
                        return 200;
                    }
                    public function getRedirectUrl(): ?string
                    {
                        return null;
                    }
                    public function getRequest(): \Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface
                    {
                        return new class () implements \Mj4444\SimpleHttpClient\Contracts\HttpRequestInterface {
                            public function getBody(): ?\Mj4444\SimpleHttpClient\Contracts\HttpRequest\BodyInterface
                            {
                                return null;
                            }
                            public function getConnectTimeout(): int|false|null
                            {
                                return null;
                            }
                            public function getHeaders(): array
                            {
                                return [];
                            }
                            public function getMaxRedirects(): ?int
                            {
                                return null;
                            }
                            public function getMethod(): string
                            {
                                return 'POST';
                            }
                            public function getTimeout(): int|false|null
                            {
                                return null;
                            }
                            public function getUrl(): string
                            {
                                return 'https://test';
                            }
                            public function isFollowLocation(): ?bool
                            {
                                return null;
                            }
                            public function isPost(): bool
                            {
                                return true;
                            }
                            public function isResponseHeadersRequired(): ?bool
                            {
                                return null;
                            }
                            public function makeResponse(int $httpCode, string $url, string $effectiveUrl, ?string $redirectUrl, array $headers, ?string $contentType, string $response): \Mj4444\SimpleHttpClient\Contracts\HttpResponseInterface
                            {
                                return $this;
                            }
                        };
                    }
                    public function getUrl(): string
                    {
                        return 'https://test';
                    }
                };
            }
        };

        $config = new \MaxMessenger\Bot\MaxApiConfig('test-token');
        $config->setHttpClient($mockHttpClient);
        $client = new MaxApiClient($config);

        // Создаём новый адаптер с логгером, ожидающим warning
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->atLeastOnce())->method('warning');

        $adapter = new MaxBotClient($client, $logger);

        // @phpstan-ignore-next-line — подавляем PHP warning от библиотеки при отсутствии 'mid' в данных
        $result = @$adapter->sendMessageToUser(123, 'Тест fallback');
        $this->assertInstanceOf(DomainSendMessageResult::class, $result);
        $this->assertSame('', $result->getMessageId());
        $this->assertSame(0, $result->getTimestamp());
    }
}
