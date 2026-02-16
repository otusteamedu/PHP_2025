<?php
declare(strict_types=1);

namespace Ak\Hw\Test;

use Ak\Hw\Application\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrderValidateTest extends TestCase
{
    private Validator $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }

    public static function getValidData(): array
    {
        return [
            "card_number" => "4111 1111 1111 1111",
            "card_holder" => "John Doe",
            "card_expiration" => "12/28",
            "cvv" => "602",
            "sum" => 10.1,
            "order_number" => "1"
        ];
    }

    public function testValidationWithValidData(): void
    {
        $errors = $this->validator->validate(self::getValidData());
        $this->assertCount(0, $errors, "Validation failed for valid data: " . json_encode($errors));
    }

    public static function invalidCardNumberProvider(): array
    {
        return [
            'empty' => ['', Validator::E_IS_EMPTY],
            'null' => [null, Validator::E_IS_EMPTY],
            'not a string' => [12345, Validator::E_INVALID_FORMAT],
            'too short' => ['4111 1111', Validator::E_INVALID_FORMAT],
            'with letters' => ['4111 1111 1111 111a', Validator::E_INVALID_FORMAT],
            'wrong separator' => ['4111-1111-1111-1111', Validator::E_INVALID_FORMAT],
        ];
    }

    public static function invalidCardHolderProvider(): array
    {
        return [
            'empty' => ['', Validator::E_IS_EMPTY],
            'null' => [null, Validator::E_IS_EMPTY],
            'with numbers' => ['John Doe 123', Validator::E_INVALID_FORMAT],
            'with special chars' => ['John@Doe', Validator::E_INVALID_FORMAT],
            'too many spaces' => ['John  Doe', Validator::E_INVALID_FORMAT],
        ];
    }

    public static function invalidExpirationProvider(): array
    {
        return [
            'empty' => ['', Validator::E_IS_EMPTY],
            'null' => [null, Validator::E_IS_EMPTY],
            'invalid format' => ['12-25', Validator::E_INVALID_FORMAT],
            'month 00' => ['00/25', Validator::E_INVALID_FORMAT],
            'month 13' => ['13/25', Validator::E_INVALID_FORMAT],
            'expired card' => ['01/20', Validator::E_DATE_EXPIRED],
        ];
    }

    public static function invalidCvvProvider(): array
    {
        return [
            'empty' => ['', Validator::E_IS_EMPTY],
            'null' => [null, Validator::E_IS_EMPTY],
            'too short' => ['12', Validator::E_INVALID_FORMAT],
            'too long' => ['12345', Validator::E_INVALID_FORMAT],
            'with letters' => ['12a', Validator::E_INVALID_FORMAT],
        ];
    }

    public static function invalidSumProvider(): array
    {
        return [
            'empty' => ['', Validator::E_IS_EMPTY],
            'null' => [null, Validator::E_IS_EMPTY],
            'not a number' => ['abc', Validator::E_NOT_NUMERIC],
            'zero' => [0, Validator::E_VALUE_NOT_POSITIVE],
            'negative' => [-10, Validator::E_VALUE_NOT_POSITIVE],
        ];
    }

    public static function invalidOrderNumberProvider(): array
    {
        return [
            'empty' => ['', Validator::E_IS_EMPTY],
            'null' => [null, Validator::E_IS_EMPTY],
            'only spaces' => ['   ', Validator::E_IS_EMPTY],
        ];
    }



    #[DataProvider('invalidCardNumberProvider')]
    public function testInvalidCardNumber($value, $expectedCode): void
    {
        $data = self::getValidData();
        $data['card_number'] = $value;
        $errors = $this->validator->validate($data);
        $this->assertSame($expectedCode, $errors['card_number']['code']);
    }

    #[DataProvider('invalidCardHolderProvider')]
    public function testInvalidCardHolder($value, $expectedCode): void
    {
        $data = self::getValidData();
        $data['card_holder'] = $value;
        $errors = $this->validator->validate($data);
        $this->assertSame($expectedCode, $errors['card_holder']['code']);
    }

    #[DataProvider('invalidExpirationProvider')]
    public function testInvalidExpiration($value, $expectedCode): void
    {
        $data = self::getValidData();
        $data['card_expiration'] = $value;
        $errors = $this->validator->validate($data);
        $this->assertSame($expectedCode, $errors['card_expiration']['code']);
    }

    #[DataProvider('invalidCvvProvider')]
    public function testInvalidCvv($value, $expectedCode): void
    {
        $data = self::getValidData();
        $data['cvv'] = $value;
        $errors = $this->validator->validate($data);
        $this->assertSame($expectedCode, $errors['cvv']['code']);
    }

    #[DataProvider('invalidSumProvider')]
    public function testInvalidSum($value, $expectedCode): void
    {
        $data = self::getValidData();
        $data['sum'] = $value;
        $errors = $this->validator->validate($data);
        $this->assertSame($expectedCode, $errors['sum']['code']);
    }

    #[DataProvider('invalidOrderNumberProvider')]
    public function testInvalidOrderNumber($value, $expectedCode): void
    {
        $data = self::getValidData();
        $data['order_number'] = $value;
        $errors = $this->validator->validate($data);
        $this->assertSame($expectedCode, $errors['order_number']['code']);
    }
}
