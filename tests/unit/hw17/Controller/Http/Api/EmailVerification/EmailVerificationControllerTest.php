<?php

declare(strict_types=1);

namespace UnitTests\hw17\Controller\Http\Api\EmailVerification;

use App\Controller\Http\Api\EmailVerification\EmailVerificationController;
use App\Core\Http\Message\Request;
use App\Domain\EmailVerification\EmailVerifier;
use PHPUnit\Framework\TestCase;

class EmailVerificationControllerTest extends TestCase
{
    /**
     * Если payload содержит поле emails с валидным массивом строк,
     * то контроллер возвращает JSON с success: true и списком валидных email.
     */
    public function testValidPayloadReturnsSuccessWithValidEmails(): void
    {
        $payload = [
            'emails' => [
                'valid@example.com',
                'invalid-no-at',
                'another@valid.net',
            ],
        ];

        $request = Request::fromArray($payload);

        $verifierMock = $this->createMock(EmailVerifier::class);
        $verifierMock
            ->expects($this->once())
            ->method('getValidEmails')
            ->with($payload['emails'])
            ->willReturn(['valid@example.com', 'another@valid.net']);

        $controller = new EmailVerificationController($request, $verifierMock);
        $response = $controller->verifyEmails();

        $this->assertEquals(200, $response->getHttpCode());
        $this->assertTrue($response->isJson());

        $data = json_decode($response->getContent(), true);
        $this->assertNotNull($data);
        $this->assertTrue($data['success']);
        $this->assertEquals(['valid@example.com', 'another@valid.net'], $data['validEmails']);
    }

    /**
     * Если в payload отсутствует поле emails,
     * то контроллер выбрасывает MissingPayloadFieldException и возвращает 400.
     */
    public function testMissingEmailsFieldReturns400(): void
    {
        $payload = ['otherField' => 'some-value'];
        $request = Request::fromArray($payload);

        $verifierMock = $this->createMock(EmailVerifier::class);
        $verifierMock->expects($this->never())->method('getValidEmails');

        $controller = new EmailVerificationController($request, $verifierMock);
        $response = $controller->verifyEmails();

        $this->assertEquals(400, $response->getHttpCode());
        $this->assertTrue($response->isJson());

        $data = json_decode($response->getContent(), true);
        $this->assertNotNull($data);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Missing required field: emails', $data['error']);
    }

    /**
     * Если поле emails присутствует, но не является массивом,
     * то контроллер выбрасывает InvalidPayloadTypeException и возвращает 400.
     */
    public function testEmailsNotArrayReturns400(): void
    {
        $payload = ['emails' => 'not-an-array'];
        $request = Request::fromArray($payload);

        $verifierMock = $this->createMock(EmailVerifier::class);
        $verifierMock->expects($this->never())->method('getValidEmails');

        $controller = new EmailVerificationController($request, $verifierMock);
        $response = $controller->verifyEmails();

        $this->assertEquals(400, $response->getHttpCode());
        $this->assertTrue($response->isJson());

        $data = json_decode($response->getContent(), true);
        $this->assertNotNull($data);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Field "emails" must be an array of strings', $data['error']);
    }

    /**
     * Если массив emails содержит хотя бы один элемент, который не является строкой,
     * то контроллер выбрасывает InvalidPayloadTypeException и возвращает 400.
     */
    public function testEmailsArrayWithNonStringReturns400(): void
    {
        $payload = [
            'emails' => [
                'valid@example.com',
                123,
                'another@test.org',
            ],
        ];

        $request = Request::fromArray($payload);

        $verifierMock = $this->createMock(EmailVerifier::class);
        $verifierMock->expects($this->never())->method('getValidEmails');

        $controller = new EmailVerificationController($request, $verifierMock);
        $response = $controller->verifyEmails();

        $this->assertEquals(400, $response->getHttpCode());
        $this->assertTrue($response->isJson());

        $data = json_decode($response->getContent(), true);
        $this->assertNotNull($data);
        $this->assertFalse($data['success']);
        $this->assertStringContainsString('Field "emails" must contain only strings', $data['error']);
    }

    /**
     * Если массив emails пуст, то контроллер возвращает success: true
     * и пустой массив validEmails.
     */
    public function testEmptyEmailsArrayReturnsEmptyResult(): void
    {
        $payload = ['emails' => []];
        $request = Request::fromArray($payload);

        $verifierMock = $this->createMock(EmailVerifier::class);
        $verifierMock
            ->expects($this->once())
            ->method('getValidEmails')
            ->with($payload['emails'])
            ->willReturn([]);

        $controller = new EmailVerificationController($request, $verifierMock);
        $response = $controller->verifyEmails();

        $this->assertEquals(200, $response->getHttpCode());
        $this->assertTrue($response->isJson());

        $data = json_decode($response->getContent(), true);
        $this->assertNotNull($data);
        $this->assertTrue($data['success']);
        $this->assertEquals([], $data['validEmails']);
    }

    /**
     * Если все элементы массива emails невалидны,
     * то контроллер возвращает success: true и пустой массив validEmails.
     */
    public function testAllInvalidEmailsReturnsEmptyValidList(): void
    {
        $payload = [
            'emails' => [
                'not-an-email',
                'no-at-sign',
                '@no-local-part',
            ],
        ];

        $request = Request::fromArray($payload);

        $verifierMock = $this->createMock(EmailVerifier::class);
        $verifierMock
            ->expects($this->once())
            ->method('getValidEmails')
            ->with($payload['emails'])
            ->willReturn([]);

        $controller = new EmailVerificationController($request, $verifierMock);
        $response = $controller->verifyEmails();

        $this->assertEquals(200, $response->getHttpCode());
        $this->assertTrue($response->isJson());

        $data = json_decode($response->getContent(), true);
        $this->assertNotNull($data);
        $this->assertTrue($data['success']);
        $this->assertEquals([], $data['validEmails']);
    }
}
