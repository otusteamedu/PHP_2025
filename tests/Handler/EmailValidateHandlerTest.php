<?php

declare(strict_types=1);

namespace Tests\Handler;

use App\Dto\EmailValidateEntryDto;
use App\Dto\EmailValidateResultDto;
use App\Handler\EmailValidateHandler;
use App\Service\EmailValidationService;
use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\Exception as MockException;

class EmailValidateHandlerTest extends TestCase
{
    private const FAILED_RESPONSE_CODE = 400;
    private const FAILED_RESPONSE_MESSAGE = 'Email list is not valid';
    private const SUCCESS_RESPONSE_CODE = 200;
    private const SUCCESS_RESPONSE_MESSAGE = 'Email list is valid';

    public function setUp(): void
    {
        error_reporting(E_ALL);
    }

    /**
     * @throws Exception
     * @throws MockException
     */
    public function testHandleEmptyEmailList(): void
    {
        $entryDto = new EmailValidateEntryDto(
            emailList: [],
        );
        $mockHandler = $this->mockValidateHandler();
        $responseDto = $mockHandler->handle($entryDto);

        $decodedResponse = json_decode($responseDto->getResponseMessage(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals(self::FAILED_RESPONSE_CODE, $responseDto->code);
        $this->assertEquals(self::FAILED_RESPONSE_MESSAGE, $decodedResponse['message']);
        $this->assertEmpty($decodedResponse['data']['invalidEmailList']);
    }

    /**
     * @throws Exception
     * @throws MockException
     */
    public function testHandleValidEmailList(): void
    {
        $validEmailList = ['some_email1', 'some_email2'];
        $entryDto = new EmailValidateEntryDto(
            emailList: $validEmailList,
        );
        $mockHandler = $this->mockValidateHandler(new EmailValidateResultDto(
            validEmailList: $validEmailList,
            isValidEmailList: true,
        ));
        $responseDto = $mockHandler->handle($entryDto);

        $decodedResponse = json_decode($responseDto->getResponseMessage(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals(self::SUCCESS_RESPONSE_CODE, $responseDto->code);
        $this->assertEquals(self::SUCCESS_RESPONSE_MESSAGE, $decodedResponse['message']);
        $this->assertEmpty($decodedResponse['data']);
    }

    /**
     * @throws Exception
     * @throws MockException
     */
    public function testHandleInvalidEmailList(): void
    {
        $invalidEmailList = ['some_email1', 'some_email2'];
        $entryDto = new EmailValidateEntryDto(
            emailList: $invalidEmailList,
        );
        $mockHandler = $this->mockValidateHandler(new EmailValidateResultDto(invalidEmailList: $invalidEmailList));
        $responseDto = $mockHandler->handle($entryDto);

        $decodedResponse = json_decode($responseDto->getResponseMessage(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals(self::FAILED_RESPONSE_CODE, $responseDto->code);
        $this->assertEquals(self::FAILED_RESPONSE_MESSAGE, $decodedResponse['message']);
        $this->assertEquals($invalidEmailList, $decodedResponse['data']['invalidEmailList']);
    }

    /**
     * @throws Exception
     * @throws MockException
     */
    public function testPartiallyInvalidEmailList(): void
    {
        $validEmailList = ['some_email1', 'some_email2'];
        $invalidEmailList = ['some_email3', 'some_email4'];
        $entryDto = new EmailValidateEntryDto(
            emailList: $invalidEmailList,
        );
        $mockHandler = $this->mockValidateHandler(new EmailValidateResultDto(
            validEmailList: $validEmailList,
            invalidEmailList: $invalidEmailList
        ));
        $responseDto = $mockHandler->handle($entryDto);

        $decodedResponse = json_decode($responseDto->getResponseMessage(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertEquals(self::FAILED_RESPONSE_CODE, $responseDto->code);
        $this->assertEquals(self::FAILED_RESPONSE_MESSAGE, $decodedResponse['message']);
        $this->assertEquals($invalidEmailList, $decodedResponse['data']['invalidEmailList']);
    }

    /**
     * @throws MockException
     */
    protected function mockValidateHandler(
        EmailValidateResultDto $checkEmailListReturnValue = new EmailValidateResultDto(),
    ): EmailValidateHandler {
        $mockService = $this->createMock(EmailValidationService::class);
        $mockService
            ->method('checkEmailList')
            ->willReturn($checkEmailListReturnValue);

        return new EmailValidateHandler($mockService);
    }
}
