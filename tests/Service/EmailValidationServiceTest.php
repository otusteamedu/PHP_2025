<?php

declare(strict_types=1);

namespace Tests\Service;

use App\RequestService\SomeProviderNameRequestService;
use App\Service\EmailValidationService;
use App\Validator\DefaultEmailValidator;
use App\Validator\DnsEmailValidator;
use App\Validator\SendConfirmationEmailValidator;
use Exception;
use PHPUnit\Framework\TestCase;

class EmailValidationServiceTest extends TestCase
{
    public function setUp(): void
    {
        error_reporting(E_ALL);
    }

    /**
     * @throws Exception
     */
    public function testCheckEmailListWithEmptyEmailList(): void
    {
        $emailList = [];

        $validationService = $this->mockValidationService();

        $resultDto = $validationService->checkEmailList($emailList);

        $this->assertFalse($resultDto->isValidEmailList);
        $this->assertEmpty($resultDto->validEmailList);
        $this->assertEmpty($resultDto->invalidEmailList);
    }

    /**
     * @throws Exception
     */
    public function testCheckEmailListWithNoValidator(): void
    {
        $validEmailList = [
            'test@test',
            'test1234@yandex.ru',
            'http://example.com',
            '',
        ];

        $validationService = $this->mockValidationService();

        $resultDto = $validationService->checkEmailList($validEmailList);

        $this->assertTrue($resultDto->isValidEmailList);
        $this->assertEquals($validEmailList, $resultDto->validEmailList);
        $this->assertEmpty($resultDto->invalidEmailList);
    }

    /**
     * @throws Exception
     */
    public function testCheckEmailListWithNoValidatorAndInvalidEmail(): void
    {
        $validEmailList = [
            'test@test',
            'test1234@yandex.ru',
            'http://example.com',
            '',
        ];
        $invalidEmailList = [
            123,
        ];

        $validationService = $this->mockValidationService();

        $resultDto = $validationService->checkEmailList(array_merge($validEmailList, $invalidEmailList));

        $this->assertFalse($resultDto->isValidEmailList);
        $this->assertEquals($validEmailList, $resultDto->validEmailList);
        $this->assertEquals($invalidEmailList, $resultDto->invalidEmailList);
    }

    /**
     * @throws Exception
     */
    public function testCheckEmailListWithAllValidatorAndInvalidEmail(): void
    {
        $validEmailList = [
            'test1234@yandex.ru',
        ];
        $invalidEmailList = [
            'http://example.com',
            'test@test.test',
            123,
            '',
        ];

        $validationService = $this->mockValidationService(
            [
                DefaultEmailValidator::class,
                SendConfirmationEmailValidator::class,
                DnsEmailValidator::class,
            ],
        );

        $resultDto = $validationService->checkEmailList(array_merge($validEmailList, $invalidEmailList));

        $this->assertFalse($resultDto->isValidEmailList);
        $this->assertEquals($validEmailList, $resultDto->validEmailList);
        $this->assertEquals($invalidEmailList, $resultDto->invalidEmailList);
    }

    /**
     * @throws Exception
     */
    public function testCheckEmailListWithAllValidator(): void
    {
        $validEmailList = [
            'test1234@yandex.ru',
            'test123g2ey1eg1824@yandex.ru',
        ];

        $validationService = $this->mockValidationService(
            [
                DefaultEmailValidator::class,
                SendConfirmationEmailValidator::class,
                DnsEmailValidator::class,
            ],
        );

        $resultDto = $validationService->checkEmailList($validEmailList);

        $this->assertTrue($resultDto->isValidEmailList);
        $this->assertEquals($validEmailList, $resultDto->validEmailList);
        $this->assertEmpty($resultDto->invalidEmailList);
    }

    protected function mockValidationService(array $validatorNameList = []): EmailValidationService
    {
        $validationService = new EmailValidationService();

        foreach ($validatorNameList as $validatorName) {
            $validator = match ($validatorName) {
                DefaultEmailValidator::class => new DefaultEmailValidator(),
                DnsEmailValidator::class => new DnsEmailValidator(),
                SendConfirmationEmailValidator::class => new SendConfirmationEmailValidator(new SomeProviderNameRequestService()),
            };

            $validationService->setValidator($validator);
        }

        return $validationService;
    }
}
