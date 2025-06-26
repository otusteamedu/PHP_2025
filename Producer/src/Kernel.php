<?php

namespace Producer;

use Producer\Application\BankDetail\BankDetailUseCase;
use Producer\Application\Http\Response;
use Producer\Application\Queue\RabbitMQ;
use Producer\Domain\DTO\BankDetailDTO;
use Producer\Infrastructure\BankDetail\RabbitMQNotifier;
use Producer\Infrastructure\Exception\ValidationException;
use Producer\Infrastructure\Task\ValidateBankDetailTask;
use Exception;
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

            (new ValidateBankDetailTask())->run($request);

            (new BankDetailUseCase(
                new RabbitMQNotifier(new RabbitMQ())
            ))->run(new BankDetailDTO(
                bik: $request['bik'],
                account: $request['account'],
                client: $request['client'],
                bank: $request['bank'],
            ));

            $response = new Response([], 200, 'Ok');
        } catch (ValidationException $e) {
            $response = new Response([], $e->getCode(), $e->getMessage());
        } catch (Throwable) {
            $response = new Response([], 500, 'Что-то пошло не так!');
        } finally {
            $response->init();
        }
    }
}