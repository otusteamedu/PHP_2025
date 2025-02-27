<?php declare(strict_types=1);

namespace classes;


class StringHandler
{
    protected StringValidator $stringValidator;

    public function __construct()
    {
        $this->stringValidator = new StringValidator();
    }

    public function handleString(string $str)
    {
        try {
            if ($this->stringValidator->isStringNotExists($str)) {
                throw new \RuntimeException('Не передан или передан пустой параметр "string"');
            }

            if (str_contains($str, '(') || str_contains($str, ')')) {
                if (!$this->stringValidator->checkBrackets($str)) {
                    throw new \RuntimeException('Неверное соотношение открытых и закрытых скобок');
                }
            }


            http_response_code(200);
            return json_encode([
                'string' => $_POST['string'],
                'status' => 'success',
                'message' => 'Строка успешно обработана',
            ]);
        } catch (\Exception $exception) {
            http_response_code(400);
            return json_encode([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ]);
        }
    }

}