<?php

namespace Blarkinov\RabbitMq\Controllers;

use Blarkinov\RabbitMq\Http\DTO\BankStatementRequestDto;
use Blarkinov\RabbitMq\Http\Response;
use Blarkinov\RabbitMq\Models\BankStatement;
use Blarkinov\RabbitMq\Models\Event\WorkerEvent;
use Blarkinov\RabbitMq\Service\DataBaseMock;
use Blarkinov\RabbitMq\Service\RabbitMq;
use Blarkinov\RabbitMq\Service\Validator;

class BankStatementController
{
    private const QUEUE_NAME = 'bank_statement';

    private Response $response;
    private Validator $validator;
    private RabbitMq $rabbitmq;

    public function __construct()
    {
        $this->response = new Response();
        $this->validator = new Validator();
        $this->rabbitmq = new RabbitMq;
    }

    public function push()
    {
        try {
            $this->validator->bankStatementPush();

            $requestDto = new BankStatementRequestDto($_POST['dateFrom'], $_POST['dateTo'], $_POST['transactionType']);

            $this->rabbitmq->set(self::QUEUE_NAME, $requestDto);

            $this->response->send(200, ['status' => 'success', 'message' => 'your request has been accepted for processing']);
        } catch (\Throwable $th) {
            $this->response->send(400, ['status' => 'failed', 'error' => $th->getMessage()]);
        }
    }

    public function get()
    {
        try {
            $this->validator->bankStatementGet();

            $bankStatementCollection = (new DataBaseMock)->getBankStatementTable();

            $requestDto = $this->rabbitmq->get(self::QUEUE_NAME);

            if ($requestDto) {
                $filterCollection = $bankStatementCollection->filter($requestDto);
                if ($filterCollection->isEmpty())
                    $this->response->send(200, ['status' => 'success', ' message' => 'no bank statement with current filters found']);
                else
                    $this->response->send(200, ['status' => 'success', ' message' => 'bank statements with current filters found', 'data' => $filterCollection]);
            } else {
                $this->response->send(200, ['status' => 'success', ' message' => 'bank statement not found']);
            }
        } catch (\Throwable $th) {
            $this->response->send(400, ['status' => 'failed', 'error' => $th->getMessage()]);
        }
    }
}
