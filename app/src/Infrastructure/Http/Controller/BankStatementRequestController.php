<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controller;

use App\Application\UseCase\SendBankStatementRequest\SendBankStatementRequestRequest;
use App\Application\UseCase\SendBankStatementRequest\SendBankStatementRequestUseCase;
use App\Application\UseCase\SendBankStatementRequest\SendBankStatementRequestValidator;
use App\Infrastructure\Http\Exception\MethodNotAllowedHttpException;
use App\Infrastructure\Http\Exception\ServerErrorHttpException;
use App\Infrastructure\Http\Response;
use App\Infrastructure\Rabbit\Config;
use App\Infrastructure\Rabbit\Producer\Producer;
use Throwable;

/**
 * Class BankStatementRequestController
 * @package App\Infrastructure\Http\Controller
 */
class BankStatementRequestController extends BaseController
{
    /**
     * @return Response
     * @throws ServerErrorHttpException
     */
    public function actionSend(): Response
    {
        try {
            $request = $this->getRequest();
            if (!$request->isPost()) {
                throw new MethodNotAllowedHttpException('Only POST method allowed');
            }

            $sendBankStatementRequestRequest = new SendBankStatementRequestRequest(
                $request->post('email'),
                $request->post('startDate'),
                $request->post('endDate'),
            );

            $validator = new SendBankStatementRequestValidator();
            $validator->validate($sendBankStatementRequestRequest);

            $producer = new Producer(new Config());
            $useCase = new SendBankStatementRequestUseCase($producer);
            $response = $useCase($sendBankStatementRequestRequest);

            return $this->asJson([
                'message' => $response->getMessage(),
            ]);
        } catch (Throwable $e) {
            throw new ServerErrorHttpException('Error sending request: ' . $e->getMessage());
        }
    }
}
