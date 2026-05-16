<?php

declare(strict_types=1);

namespace App\Presenter;

use App\DTO\StatementRequest;

/**
 * Презентер страниц формы заказа банковской выписки.
 */
final class StatementRequestPresenter
{
    private const ACCEPTED_WITH_EMAIL = 'Запрос принят в обработку. Выписка будет отправлена на указанный email.';
    private const ACCEPTED_WITH_BRANCH_PICKUP = 'Запрос принят в обработку. Выписку можно будет забрать в ближайшем отделении банка через 3 рабочих дня.';

    /**
     * @param string $templatesPath Путь к директории с шаблонами.
     */
    public function __construct(private readonly string $templatesPath)
    {
    }

    /**
     * Формирует страницу с формой заказа выписки.
     *
     * @param string[] $errors
     * @param array<string, string> $previousInput
     * @return string HTML-страница формы.
     */
    public function showForm(array $errors = [], array $previousInput = []): string
    {
        return $this->renderPage('statement_request_form.php', [
            'title' => 'Заказ банковской выписки',
            'errors' => $errors,
            'previousInput' => $previousInput,
        ]);
    }

    /**
     * Формирует страницу успешного принятия заявки.
     *
     * @param StatementRequest $request Заявка на выписку.
     * @return string HTML-страница результата.
     */
    public function showAccepted(StatementRequest $request): string
    {
        $message = $request->hasEmail()
            ? self::ACCEPTED_WITH_EMAIL
            : self::ACCEPTED_WITH_BRANCH_PICKUP;

        return $this->renderPage('statement_request_accepted.php', [
            'title' => 'Заявка принята',
            'message' => $message,
        ]);
    }

    /**
     * Рендерит шаблон внутри общего layout.
     *
     * @param string $template Имя шаблона страницы.
     * @param array<string, mixed> $data
     * @return string Готовая HTML-страница.
     */
    private function renderPage(string $template, array $data): string
    {
        extract($data);

        ob_start();
        require $this->templatesPath . '/' . $template;
        $content = ob_get_clean();

        ob_start();
        require $this->templatesPath . '/layout.php';

        return ob_get_clean();
    }
}
