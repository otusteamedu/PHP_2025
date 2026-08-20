<?php

declare(strict_types=1);

namespace IntegrationTests\hw17\Api\EmailVerification;

use App\Controller\Http\Api\EmailVerification\EmailVerificationController;
use App\Core\Http\Message\Request;
use App\Domain\EmailVerification\EmailVerifier;
use App\Domain\Shared\Validator\DnsMxRecordValidator;
use App\Domain\Shared\Validator\EmailFormatValidator;
use App\Domain\Shared\Validator\EmailValidator;
use App\Infrastructure\Resolver\DnsResolver;
use App\Infrastructure\Resolver\DnsResolverInterface;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционные тесты для модуля EmailVerification.
 * Проверяют связку: EmailVerificationController → EmailVerifier → валидаторы → DnsResolver
 */
class EmailVerificationIntegrationTest extends TestCase
{
    /**
     * Если payload содержит email с доменом, у которого есть MX‑запись (например, example.com)
     * и используется реальный DnsResolver, то контроллер возвращает 200 и email попадает в validEmails.
     */
    public function testEmailWithValidDomainReturnsValidInList(): void
    {
        $email = 'test@example.com';
        $payload = ['emails' => [$email]];

        $request = Request::fromArray($payload);

        $resolver = new DnsResolver();
        $dnsValidator  = new DnsMxRecordValidator($resolver);
        $formatValidator = new EmailFormatValidator();
        $emailValidator = new EmailValidator($formatValidator, $dnsValidator);
        $verifier = new EmailVerifier($emailValidator);

        $controller = new EmailVerificationController($request, $verifier);
        $response = $controller->verifyEmails();

        $this->assertEquals(200, $response->getHttpCode());
        $this->assertTrue($response->isJson());

        $data = json_decode($response->getContent(), true);
        $this->assertNotNull($data);
        $this->assertTrue($data['success']);
        $this->assertContains($email, $data['validEmails']);
    }

    /**
     * Если email содержит домен без MX‑записи,
     * то он не попадает в список validEmails, несмотря на корректный формат.
     */
    public function testEmailWithNoMxRecordReturnsNotInList(): void
    {
        $email = 'fake@any-domain.com';
        $payload = ['emails' => [$email]];

        $request = Request::fromArray($payload);

        // Stub резолвера: делаем тест детерминированным - гарантируем отсутствие MX без зависимости от сети
        $resolverStub = $this->createStub(DnsResolverInterface::class);
        $resolverStub->method('hasMxRecord')->willReturn(false);

        $dnsValidator  = new DnsMxRecordValidator($resolverStub);
        $formatValidator = new EmailFormatValidator();
        $emailValidator = new EmailValidator($formatValidator, $dnsValidator);
        $verifier = new EmailVerifier($emailValidator);

        $controller = new EmailVerificationController($request, $verifier);
        $response = $controller->verifyEmails();

        $this->assertEquals(200, $response->getHttpCode());
        $this->assertTrue($response->isJson());

        $data = json_decode($response->getContent(), true);
        $this->assertNotNull($data);
        $this->assertTrue($data['success']);
        $this->assertNotContains($email, $data['validEmails']);
    }
}
