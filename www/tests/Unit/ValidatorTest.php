<?php


namespace Tests\Unit;

use Exception;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmail;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailHost;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailParameters;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailValidation;
use Larkinov\Myapp\Services\Validator;
use Tests\Support\UnitTester;

class ValidatorTest extends \Codeception\Test\Unit
{
    private static $pathDataProvider = __DIR__ . '/../Data/Validator/';

    protected UnitTester $tester;


    /**
     * @dataProvider dataProviderValidateRequest
     */

    public function testValidateRequest($input, $expected): void
    {
        $validator = new Validator();

        $_SERVER['REQUEST_METHOD'] = $input;


        if ($expected === 'void') {
            $_POST['email'] = 'user@example.com';
            $this->assertNull($validator->validateRequest());
        } else {
            $this->expectException(Exception::class);
            $validator->validateRequest();
        }
    }

    public static function dataProviderValidateRequest()
    {
        return include self::$pathDataProvider . '/request.php';
    }

    /**
     * @dataProvider dataProviderValidateEmail
     */

    public function testValidateEmail($input, $expected)
    {
        $validator = new Validator();

        if ($input !== 'emptyTest')
            $_POST['email'] = $input;

        if ($expected === 'void')
            $this->assertNull($validator->validateEmail());
        else {
            switch ($expected) {
                case ExceptionEmailParameters::class:
                    $this->expectException(ExceptionEmailParameters::class);
                    break;
                case ExceptionEmailValidation::class:
                    $this->expectException(ExceptionEmailValidation::class);
                    break;
                case ExceptionEmailHost::class:
                    $this->expectException(ExceptionEmailHost::class);
                    break;
                default:
                    if ($expected === ExceptionEmail::class)
                        $this->expectException(ExceptionEmailHost::class);
                    else
                        $this->fail();
                    break;
            }
            $validator->validateEmail();
        }
    }

    public static function dataProviderValidateEmail()
    {
        return include self::$pathDataProvider . '/email.php';
    }
}
