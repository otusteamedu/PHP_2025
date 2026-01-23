<?php
declare(strict_types=1);

namespace App\Presentation\Actions;

use App\Application\DTO\EmailsInputDTO;
use App\Application\UseCases\VerifyEmailsUseCase;
use App\Domain\Interfaces\ActionInterface;
use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

class VerifyEmailsAction implements ActionInterface
{
    private VerifyEmailsUseCase $verifyEmailsUseCase;

    public function __construct(VerifyEmailsUseCase $verifyEmailsUseCase)
    {
        $this->verifyEmailsUseCase = $verifyEmailsUseCase;
    }

    public function supports(Request $request): bool
    {
        return $request->isPost();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $emails = array_map('trim', explode(PHP_EOL, trim($request->getPostParam('emails'))));
        $emailsInputDTO = new EmailsInputDTO(array_filter($emails));

        $result = $this->verifyEmailsUseCase->execute($emailsInputDTO);

        return Response::json($result->toArray());
    }
}
