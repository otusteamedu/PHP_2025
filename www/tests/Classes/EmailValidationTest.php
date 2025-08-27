<?php

use Larkinov\Myapp\Exceptions\Email\ExceptionEmail;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailHost;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailParameters;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailValidation;
use Larkinov\Myapp\Services\EmailValidation;
use PHPUnit\Framework\TestCase;

class EmailValidationTest extends TestCase
{

    private const PROVIDER_DATA = 'EmailValidation.json';

    /**
     * @dataProvider dataProviderIsValid
     */

    public function testIsValid($input, $expected)
    {
        $validator = new EmailValidation();

        if ($input !== 'emptyTest')
            $_POST['email'] = $input;

        if ($expected === 'void')
            $this->assertNull($validator->isValid());
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
            // $this->expectExceptionMessage('Invalid argument provided');
            $validator->isValid();
        }
    }

    public static function dataProviderIsValid()
    {
        $json = json_decode(file_get_contents(__DIR__ . '/../Data/' . self::PROVIDER_DATA), true);

        $testData = [];

        foreach ($json as $test) {
            $testData[$test['name']] = [$test['input'], $test['expected']];
        }

        return $testData;
    }
}
