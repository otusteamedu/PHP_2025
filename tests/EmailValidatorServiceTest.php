<?php

namespace Tests\Services;

use App\Exception\InvalidEmailException;
use App\Exception\NoMxRecordException;
use App\Service\EmailValidatorService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EmailValidatorServiceTest extends TestCase
{
    #[DataProvider('providerCorrectEmail')]
    public function testValidateEmailEmpty($result, $emails)
    {
        $this->assertEquals($result, EmailValidatorService::validate($emails));
    }

    public static function providerCorrectEmail()
    {
        return [
            [true, [],],
            [true, ['test@mail.ru'],],
            [true, ['raxmnova_elizavetka@mail.ru'],],
            [true, ['testa@mail.ru', 'test@gmail.com'],],
        ];
    }
    
    public static function providerIncorrectEmail()
    {
        return [
            [['test'],],
            [['test.'],],
            [['test@'],],
            [['test@mail'],],
            [['test@mail.'],],
            [['test@mail.ru', 'name@'],],
        ];

    }
    
    public static function providerIncorrectDns()
    {
        return [
            [['test@sfswfsfsffsd.ru']],
            [['name@sdfsdf.com', 'test@asdsdssd.com']],
        ];

    }

    #[DataProvider('providerIncorrectEmail')]
    public function testEmailsInvalid($emails)
    {
        $this->expectException(InvalidEmailException::class);
        EmailValidatorService::validate($emails);
    }
    
    #[DataProvider('providerIncorrectDns')]
    public function testInvalidDns($emails)
    {
        $this->expectException(NoMxRecordException::class);
        EmailValidatorService::validate($emails);
    }
}