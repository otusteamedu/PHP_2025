<?php

use Larkinov\Myapp\Exceptions\Email\ExceptionEmail;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailHost;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailParameters;
use Larkinov\Myapp\Exceptions\Email\ExceptionEmailValidation;
use Larkinov\Myapp\Services\EmailValidation;
use PHPUnit\Framework\TestCase;

class EmailValidationTest extends TestCase
{


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
        $data =
            [
                'correct_email_1' => ['user@example.com', 'void'],
                'correct_email_2' => ['user.name@example.com', 'void'],
                'correct_email_3' => ['user_name@ya.ru', 'void'],
                'correct_email_4' => ['user-name@gmail.com', 'void'],
                'correct_email_5' => ['user123@domain.io', 'void'],
                'incorrect_email_1' => ['', ExceptionEmailParameters::class],
                'incorrect_email_2' => ['emptyTest', ExceptionEmailParameters::class],
                'incorrect_email_3' => ['useruser', ExceptionEmailValidation::class],
                'incorrect_email_4' => ['@username.com', ExceptionEmailValidation::class],
                'incorrect_email_5' => ['username@.com', ExceptionEmailValidation::class],
                'incorrect_email_6' => ['username@domain..com', ExceptionEmailValidation::class],
                'incorrect_email_7' => ['username@domain,com', ExceptionEmailValidation::class],
                'incorrect_email_8' => ['username@-domain.com', ExceptionEmailValidation::class],
                'incorrect_email_9' => ['username@domain-.com', ExceptionEmailValidation::class],
                'incorrect_email_10' => ['user name@example.com', ExceptionEmailValidation::class],
                'incorrect_email_11' => ['user@.example.com', ExceptionEmailValidation::class],
                'incorrect_email_12' => ['13user@.example.com', ExceptionEmailValidation::class],
                'incorrect_email_13' => ['useruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruseruser@example.com', ExceptionEmailValidation::class],
                'incorrect_email_14' => ['user@domaindomain.com', ExceptionEmailHost::class],
                'incorrect_email_15' => ['user@incorrect.su', ExceptionEmailHost::class],
            ];

        return $data;
    }
}
