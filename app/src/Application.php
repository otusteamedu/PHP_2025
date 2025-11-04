<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\Http\Controller\BankStatementRequestController;
use App\Infrastructure\Http\Exception\HttpException;
use App\Infrastructure\Http\Exception\ServerErrorHttpException;
use App\Infrastructure\Http\Response;
use Throwable;

/**
 * Class Application
 * @package App
 */
class Application
{
    /**
     * @return void
     */
    public function run(): void
    {
        echo $this->handleRequest()->send();
    }

    /**
     * Handles the specified request.
     * @return Response
     */
    private function handleRequest(): Response
    {
        try {
            return $this->runAction();
        } catch (HttpException $e) {
            $statusCode = $e->getStatusCode();

            return Response::create(
                $statusCode,
                [
                    'name' => $e->getName(),
                    'status' => $statusCode,
                    'message' => $e->getMessage(),
                ]
            );
        } catch (Throwable $e) {
            $statusCode = 500;

            return Response::create(
                $statusCode,
                [
                    'name' => 'Internal Server Error',
                    'status' => $statusCode,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }

    /**
     * @return Response
     * @throws ServerErrorHttpException
     */
    private function runAction(): Response
    {
        return (new BankStatementRequestController())->actionSend();
    }
}
