<?php

namespace App\Request;

use App\Exceptions\ValidationException;
use App\Response\Response;
use Exception;

class Request
{
    protected $data;
    protected $method;

    /**
     * @param string $method
     * @param $data
     * @throws ValidationException
     * @throws Exception
     */
    public function __construct(string $method, $data)
    {
        if ($method !== 'POST') {
            throw new Exception('Метод не поддерживается', 405);
        }

        $this->validate($data);

        $this->method = $method;
        $this->data = $data;
    }

    /**
     * @param array $data
     * @return void
     * @throws ValidationException
     */
    protected function validate(array $data)
    {
        if (empty($data['list'])) {
            throw new ValidationException('Список строк не должен быть пустым');
        }
    }

    /**
     * @return Response
     */
    public function init(): Response
    {
        $list = $this->data['list'];

        $result = [];

        foreach ($list as $str) {
            if (is_string($str) === false) {
                $result[] = $this->setEmailError('Должна быть строкой', $str);
                continue;
            }

            if (!preg_match('/^\S+@\S+\.\S+$/', $str)) {
                $result[] = $this->setEmailError('Строка не является адресом почты', $str);
                continue;
            }

            $domain = substr(strrchr($str, "@"), 1);
            $mxExist = getmxrr($domain, $mxRecords);

            if ($mxExist === false || count($mxRecords) === 0) {
                $result[] = $this->setEmailError('MX записи не обнаружены', $str);
                continue;
            }

            $result[] = [
                'error' => false,
                'value' => $str,
            ];
        }

        return new Response($result);
    }

    /**
     * @param $message
     * @param $str
     * @return array
     */
    protected function setEmailError($message, $str): array
    {
        return [
            'error' => true,
            'value' => $str,
            'message' => $message,
        ];
    }
}