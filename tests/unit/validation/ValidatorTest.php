<?php

use PHPUnit\Framework\TestCase;
use App\validation\Validator;

class ValidatorTest extends TestCase
{
    // позитивный тест. проверим что нормальный email проходит валидацию
    public function testValidEmailFormat(): void
    {
        $validator = new Validator();
        $this->assertTrue($this->invokeIsValidEmailFormat($validator, 'test@example.com'));
    }

    //негативный тест. проверим что точно неправильный email не проходит валидацию
    public function testInvalidEmailFormat(): void
    {
        $validator = new Validator();
        $this->assertFalse($this->invokeIsValidEmailFormat($validator, 'invalid-email'));
        $this->assertFalse($this->invokeIsValidEmailFormat($validator, 'user@'));
        $this->assertFalse($this->invokeIsValidEmailFormat($validator, ''));
    }

    private function invokeIsValidEmailFormat(Validator $validator, string $email): bool
    {
        $method = new ReflectionMethod($validator, 'isValidEmailFormat');
        $method->setAccessible(true);
        return $method->invoke($validator, $email);
    }

    //тесты на MX-проверку с мокированием
    //позитивный
    public function testHasValidMxRecord(): void
    {
        $dnsMock = $this->createMock(\App\validation\DnsChecker::class);
        $dnsMock->method('hasMx')->willReturn(true);

        $validator = new \App\validation\Validator($dnsMock);
        $this->assertTrue($validator->isValid('user@domain.com'));
    }

    //негативный
    public function testHasInvalidMxRecord(): void
    {
        $dnsMock = $this->createMock(\App\validation\DnsChecker::class);
        $dnsMock->method('hasMx')->willReturn(false);

        $validator = new \App\validation\Validator($dnsMock);
        $this->assertFalse($validator->isValid('user@nonexistent.tld'));
    }

}
