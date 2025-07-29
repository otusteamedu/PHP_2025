<?

declare(strict_types=1);

class Handler
{
    private Validator $validator;

    public function __construct()
    {
        $this->registerAutoLoader();
        $this->validator = new Validator();
    }

    public function handle($method, $json): void
    {
        try {
            $this->validateMethod($method);
            $string = $this->parseValueFromJson('string', $json);
            $this->validator->validate($string);

            http_response_code(200);
            echo json_encode(
                ['message' => 'Строка корректна'],
                JSON_UNESCAPED_UNICODE
            );

        } catch (\Throwable $e) {
            http_response_code(400);
            echo json_encode(
                ['message' => $e->getMessage()],
                JSON_UNESCAPED_UNICODE
            );
        }
    }

    private function validateMethod($value): void
    {
        if ($value !== 'POST') {
            throw new Exception('Неверный метод');
        }
    }
    private function parseValueFromJson(string $key, string $body): string
    {
        $data = json_decode($body, true);

        if (!isset($data[$key])) {
            throw new Exception('Cтрока не найдена');
        }

        return $data[$key];
    }

    private function registerAutoLoader(): void
    {
        set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);
        spl_autoload_register();
    }
}