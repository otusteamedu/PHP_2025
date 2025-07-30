<?

declare(strict_types=1);

namespace Kamalo\Verificator\Service\EmailVerificatorService;

class EmailVerificatorService
{
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
        $emails = $this->parseEmails($string);

        if ($emails === []) {
            return new EmailVerificatorResponse(
                success: false,
                email: $string,
                message: "Неверный формат Email"
            );
        }

        foreach ($emails as $email) {
            if (!$this->isValidEmailDomain($email)) {
                return new EmailVerificatorResponse(
                    success: false,
                    email: $string,
                    message: "Email не содержит существующий домен"
                );
            }
        }
        return new EmailVerificatorResponse(
            success: true,
            email: $string,
            message: "Email валидный"
        );
    }

    private function parseEmails(string $string): array
    {
        if (
            preg_match(
                '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                $string,
                $matches
            )
        ) {
            return $matches;
        }

        return [];
    }

    private function isValidEmailDomain(string $email): bool
    {
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