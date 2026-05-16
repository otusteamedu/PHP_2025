<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\StatementRequest;
use App\Presenter\StatementRequestPresenter;
use App\Queue\QueuePublisherFactory;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

/**
 * Контроллер формы заказа банковской выписки.
 */
final class StatementRequestController
{
    /**
     * @param QueuePublisherFactory $queuePublisherFactory Фабрика отправителя сообщений в очередь.
     * @param StatementRequestPresenter $presenter Презентер страниц заявки.
     */
    public function __construct(
        private readonly QueuePublisherFactory $queuePublisherFactory,
        private readonly StatementRequestPresenter $presenter,
    ) {
    }

    /**
     * Обрабатывает HTTP-запрос к форме заказа выписки.
     *
     * @param string $method HTTP-метод запроса.
     * @param array<string, mixed> $post
     * @return string HTML-страница ответа.
     */
    public function handle(string $method, array $post): string
    {
        return match ($method) {
            'GET' => $this->presenter->showForm(),
            'POST' => $this->handlePost($post),
            default => $this->presenter->showForm(['Метод запроса не поддерживается.']),
        };
    }

    /**
     * Обрабатывает запрос отправки формы заказа выписки.
     *
     * @param array<string, mixed> $post
     * @return string HTML-страница ответа.
     */
    private function handlePost(array $post): string
    {
        $formData = $this->normalizeFormData($post);
        $errors = $this->validate($formData);

        if ($errors !== []) {
            return $this->presenter->showForm($errors, $formData);
        }

        $request = new StatementRequest(
            Uuid::uuid4()->toString(),
            $formData['account_number'],
            $formData['date_from'],
            $formData['date_to'],
            $formData['email'] !== '' ? $formData['email'] : null,
            date('Y-m-d H:i:s'),
        );

        $this->queuePublisherFactory->create()->publish($request->toJson());

        return $this->presenter->showAccepted($request);
    }

    /**
     * @param array<string, mixed> $post
     * @return array{email: string, account_number: string, date_from: string, date_to: string}
     */
    private function normalizeFormData(array $post): array
    {
        return [
            'email' => trim($post['email'] ?? ''),
            'account_number' => trim($post['account_number'] ?? ''),
            'date_from' => trim($post['date_from'] ?? ''),
            'date_to' => trim($post['date_to'] ?? ''),
        ];
    }

    /**
     * Проверяет данные формы заказа выписки.
     *
     * @param array{email: string, account_number: string, date_from: string, date_to: string} $data
     * @return string[] Список ошибок валидации.
     */
    private function validate(array $data): array
    {
        $errors = [];

        if ($data['email'] !== '' && filter_var($data['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Укажите корректный email или оставьте поле пустым.';
        }

        if (!preg_match('/^\d{10,34}$/', $data['account_number'])) {
            $errors[] = 'Номер счета должен содержать от 10 до 34 цифр.';
        }

        $dateFrom = $this->createDate($data['date_from']);
        $dateTo = $this->createDate($data['date_to']);

        if ($dateFrom === null) {
            $errors[] = 'Укажите корректную дату начала периода.';
        }

        if ($dateTo === null) {
            $errors[] = 'Укажите корректную дату окончания периода.';
        }

        if ($dateFrom !== null && $dateTo !== null && $dateFrom > $dateTo) {
            $errors[] = 'Дата начала периода не может быть позже даты окончания.';
        }

        if ($dateFrom !== null && $dateTo !== null && $dateTo > $dateFrom->modify('+1 year')) {
            $errors[] = 'Период выписки не может быть больше одного года.';
        }

        return $errors;
    }

    /**
     * Создает объект даты из строки формы.
     *
     * @param string $value Дата в формате YYYY-MM-DD.
     * @return DateTimeImmutable|null
     */
    private function createDate(string $value): ?DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        if ($date === false || $date->format('Y-m-d') !== $value) {
            return null;
        }

        return $date;
    }
}
