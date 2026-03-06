<?php
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\SubmitStatementDto;
use App\Application\UseCase\SubmitStatementUseCase;
use App\Presentation\Http\Request;
use App\Presentation\Http\Response;
use App\Presentation\View\TemplateRenderer;

class StatementController
{
    public function __construct(
        private readonly TemplateRenderer $renderer,
        private readonly SubmitStatementUseCase $useCase,
    ) {}

    /**
     * @throws \Exception
     */
    public function form(Request $request): Response
    {
        $content = $this->renderer->render('form.php', ['error' => null, 'old' => []]);

        return Response::html($this->renderer->render('base.php', [
            'title' => 'Statement Request',
            'content' => $content,
        ]));
    }

    /**
     * @throws \Exception
     */
    public function submit(Request $request): Response
    {
        $email = trim($request->getPostParam('email'));
        $dateFrom = trim($request->getPostParam('date_from'));
        $dateTo = trim($request->getPostParam('date_to'));

        $error = $this->validate($email, $dateFrom, $dateTo);
        if ($error !== null) {
            $content = $this->renderer->render('form.php', [
                'error' => $error,
                'old' => ['email' => $email, 'date_from' => $dateFrom, 'date_to' => $dateTo],
            ]);

            return Response::html($this->renderer->render('base.php', [
                'title' => 'Statement Request',
                'content' => $content,
            ]), 422);
        }

        try {
            $dto = new SubmitStatementDto($email, $dateFrom, $dateTo);
            $requestId = $this->useCase->handle($dto);
        } catch (\Throwable $exception) {
            return Response::html($this->renderer->render('base.php', [
                'title' => 'Service unavailable',
                'content' => '<p>Try again later</p>',
            ]), 503);
        }

        $content = $this->renderer->render('accepted.php', [
            'requestId' => $requestId,
            'email' => $email,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);

        return Response::html($this->renderer->render('base.php', [
            'title' => 'Request accepted',
            'content' => $content,
        ]));
    }

    private function validate(string $email, string $dateFrom, string $dateTo): ?string
    {
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Email is not valid';
        }

        $from = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateFrom);
        $to = \DateTimeImmutable::createFromFormat('!Y-m-d', $dateTo);

        if (!$from || $from->format('Y-m-d') !== $dateFrom) {
            return 'Date is not valid';
        }

        if (!$to || $to->format('Y-m-d') !== $dateTo) {
            return 'Date is not valid';
        }

        if ($from > $to) {
            return 'The "from" date cannot be later than the "to" date';
        }

        return null;
    }
}
