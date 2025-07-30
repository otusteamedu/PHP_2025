<?

declare(strict_types=1);

namespace Kamalo\Verificator\Service\EmailVerificatorService;

class EmailVerificatorResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $email,
        public readonly string $message
    ) {}
}