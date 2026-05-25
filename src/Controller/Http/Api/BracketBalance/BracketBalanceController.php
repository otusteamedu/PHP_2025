<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\BracketBalance;

use App\Controller\Http\AbstractController;
use App\Core\Http\Message\Request;
use App\Core\Http\Message\Response;
use App\Domain\BracketBalance\BracketBalancer;

class BracketBalanceController extends AbstractController
{
    public function __construct(
        private readonly Request $request,
        private readonly BracketBalancer $bracketBalancer,
    ) {
    }

    public function checkBracketsBalance(): Response
    {
        try {
            $payload = $this->request->getPayload();
            if (!isset($payload['string'])) {
                throw new \Exception('String parameter is not passed in request body', 400);
            }
            $isValidStr = $this->bracketBalancer->validateBracketString($payload['string']);
            $httpCode = $isValidStr ? 200 : 400;
            $message = $isValidStr ? 'Всё хорошо' : 'Всё плохо';
        } catch (\Exception $e) {
            $httpCode = $e->getCode();
            $message = $e->getMessage();
        }

        return $this->json(['response' => $message], $httpCode);
    }
}
