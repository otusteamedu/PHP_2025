<?

declare(strict_types=1);

namespace Kamalo\Verificator\App;

use Kamalo\Verificator\Service\EmailVerificatorService\EmailVerificatorService;
use Kamalo\Verificator\Service\EmailVerificatorService\EmailVerificatorResponse;

/**
 * Вспомогательный класс для тестирования сервиса
 */
class AppEmulator
{
    private EmailVerificatorService $verificator;

    private array $tests = [
        'valid.email@gmail.com',
        'user.name+tag+sorting@mail.ru',
        'test.email@subdomain.yandex.ru',
        'invalid-email@.com',
        '@missingusername.com',
        'user@nonexist.xyz',
        'user@outlook.com',
        'user@localhost',
        'user@domain..com',
        'user@mail.ru',
        'user@MAIL.RU'
    ];

    private array $results = [];

    public function __construct()
    {
        $this->verificator = new EmailVerificatorService();
    }

    public function run()
    {
        foreach ($this->tests as $test) {

            $this->addResult(
                $this->verificator->verify($test)
            );
        }

        $this->showReport();
    }

    private function showReport(): void
    {
        header('Content-Type: application/json');
        echo json_encode(
            $this->results,
            JSON_UNESCAPED_UNICODE
        );

    }
    private function addTest(string $test)
    {
        $this->tests[] = $test;
    }

    private function addResult(EmailVerificatorResponse $result)
    {
        $this->results[] = $result->email . ' - ' . $result->message;
    }
}