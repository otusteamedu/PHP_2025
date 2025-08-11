<?

declare(strict_types=1);

namespace Kamalo\Verificator\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Kamalo\Verificator\Service\EmailVerificatorService\EmailVerificatorService;


class EmailVerificatorServiceTest extends TestCase
{
    /**
     * @dataProvider emailsProvider
     */
    public function testVerify(
        string $email,
        bool $expected
    ): void {
        $service = new EmailVerificatorService();

        $this->assertEquals(
            $expected,
            $service->verify($email)->status
        );
    }

    
    public function emailsProvider()
    {
        return [
            ['valid.email@gmail.com', true], // допустимый email
            ['user.name+tag+sorting@mail.ru', true], // допустимый email
            ['test@m.mail.ru', true], // допустимый email
            ['invalid-email@.com', false], // недопустимый email - email не соответствует формату
            ['@missingusername.com', false], // недопустимый email - email не соответствует формату
            ['user@nonexist.xyz', false], // недопустимый email - недопустимый домен
            ['user@outlook.com', true], // допустимый email
            ['user@localhost', false], // недопустимый email - недопустимый домен
            ['user@domain..com', false], // недопустимый email - не соответствует формату
            ['user@mail.ru', true], // допустимый email
            ['user@MAIL.RU', true], // допустимый email?
        ];
    }
}