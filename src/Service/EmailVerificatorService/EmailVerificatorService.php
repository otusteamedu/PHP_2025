<?

declare(strict_types=1);

namespace Kamalo\Verificator\Service\EmailVerificatorService;

class EmailVerificatorService
{
    private array $emails = [];
    private string $errorMessage = 'Некорректный формат Email';

    public function verifyPacket(string ...$strings): array
    {
        $result = [];

        foreach ($strings as $string) {
            $result[] = $this->verify($string);
        }

        return $result;
    }
    public function verify(string $string): EmailVerificatorResponse
    {
        if (
            $this->avalibleParse($string)
            && $this->isValidEmailDomain($this->emails[0])
        ) {
            return new EmailVerificatorResponse(
                true,
                $this->emails[0],
                "Email валидный"
            );
        }

        return new EmailVerificatorResponse(
            false,
            $string,
            $this->errorMessage
        );
    }

    private function avalibleParse(string $string): bool
    {
        return (bool) preg_match(
            '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            $string,
            $this->emails
        );
    }

    private function isValidEmailDomain(string $email): bool
    {
        $this->errorMessage = 'Email не содержит существующий домен';

        $domain = strtolower(
            substr(
                strrchr(
                    $email,
                    "@"
                ),
                1
            )
        );

        return getmxrr($domain, $mxhosts);
    }
}