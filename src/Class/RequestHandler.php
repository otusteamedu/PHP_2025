<?

declare(strict_types=1);

namespace Kamalo\Balancer\Class;
class RequestHandler
{
    public function __construct(private Validator $validator)
    {
    }

    public function handle(): void
    {
        try {
            $this->validateMethod($this->getRequestMethod());

            $string = $this->parseValueFromJson(
                'string',
                $this->getRequestBody()
            );

            if ($this->validator->isValid($string)) {
                $this->sendResponse(
                    200,
                    'Строка корректна'
                );
            } else {
                $this->sendResponse(
                    400,
                    'Строка не корректна: некорректное количество открывающих и закрывающих скобок'
                );
            }
        } catch (\Throwable $e) {
            $this->sendResponse(
                400,
                $e->getMessage()
            );
        }
    }

    private function validateMethod($value): void
    {
        if ($value !== 'POST') {
            throw new \Exception('Неверный метод');
        }
    }

    private function parseValueFromJson(string $key, string $body): string
    {
        $data = json_decode($body, true);

        if (!isset($data[$key])) {
            throw new \Exception('Cтрока не найдена');
        }

        if (empty($data[$key])) {
            throw new \Exception('Строка не может быть пустой');
        }

        return $data[$key];
    }

    private function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function getRequestBody()
    {
        return file_get_contents('php://input');
    }

    private function sendResponse(int $code, string $message): void
    {
        http_response_code($code);
        echo json_encode(
            ['message' => $message],
            JSON_UNESCAPED_UNICODE
        );
    }
}