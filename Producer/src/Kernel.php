<?php

namespace Producer;

use Exception;
use Producer\Exception\ValidationException;
use Producer\Http\Response;
use Producer\Service\RabbitMQ;
use Throwable;

class Kernel
{
    /**
     * Отправка в очередь RabbitMQ данных по банку
     *
     * @return void
     */
    public function run(): void {
        try {
            $request = $_REQUEST;

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Принимает только POST запросы', 400);
            }

            $this->validation($request);

            $rabbitMQ = new RabbitMQ();

            $data = json_encode([
                'bik' => $request['bik'],
                'account' => $request['account'],
                'client' => $request['client'],
                'bank' => $request['bank'],
            ]);

            $rabbitMQ->trySendTest($data);

            $response = new Response([], 200, 'Ok');
        } catch (ValidationException|Exception $e) {
            $response = new Response([], $e->getCode(), $e->getMessage());
        } catch (Throwable) {
            $response = new Response([], 500, 'Что-то пошло не так!');
        } finally {
            $response->init();
        }
    }

    /**
     * @throws ValidationException
     */
    protected function validation(array $data): void {
        $account = $data['account'] ?? null;
        if (!is_string($account) || strlen($account) != 20 || empty($account)) {
            throw new ValidationException('Строка account должна быть 20 символам');
        }

        $client = $data['client'] ?? null;
        if (!is_string($client) || empty($client)) {
            throw new ValidationException('Строка client должна быть ФИО');
        }

        $bank = $data['bank'] ?? null;
        if (!is_string($bank) || empty($bank)) {
            throw new ValidationException('Строка bank должна быть Наименованием банка');
        }

        $bik = $data['bik'] ?? null;
        if (!is_string($bik) || strlen($bik) != 9 || empty($bik)) {
            throw new ValidationException('Строка bik должна быть 9 символам');
        }
    }
}