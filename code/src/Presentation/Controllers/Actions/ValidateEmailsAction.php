<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Actions;

use App\Application\Interfaces\ValidateEmailsUseCaseInterface;
use App\Domain\DTO\EmailValidationRequest;
use App\Presentation\Interfaces\ActionInterface;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class ValidateEmailsAction implements ActionInterface
{
    public function __construct(
        private readonly ValidateEmailsUseCaseInterface $validateEmailsUseCase
    ) {}

    public function supports(Request $request): bool
    {
        return $request->isPost() && $request->getPath() === '/emails';
    }

    public function handle(Request $request): Response
    {
        $emails = $request->getBody();

        if (!$emails) {
            return Response::error('Необходимо передать массив email-адресов в формате JSON.');
        }

        $validationRequest = new EmailValidationRequest($emails);

        $results = $this->validateEmailsUseCase->execute($validationRequest);

        $data = array_map(
            fn($result) => $result->toArray(),
            $results
        );

        return Response::success($data);
    }
}
