<?php

declare(strict_types=1);

namespace MkdBot\Application\UseCase;

use MkdBot\Domain\Enum\ContactType;
use MkdBot\Domain\Interface\ContactRepositoryInterface;

/**
 * Получение контактов УК / совета дома
 */
class GetContacts
{
    private const MAX_MESSAGE_LENGTH = 4000; // Лимит Max API

    public function __construct(
        private readonly ContactRepositoryInterface $contactRepo,
    ) {
    }

    /**
     * Возвращает отформатированный текст контактов
     */
    public function execute(string $type): string
    {
        $contactType = ContactType::from($type);
        $contacts = $this->contactRepo->findByType($contactType);

        if (empty($contacts)) {
            return $contactType === ContactType::Uk
                ? '🏢 Контакты управляющей компании не найдены'
                : '🏠 Контакты совета дома не найдены';
        }

        $text = '';
        foreach ($contacts as $contact) {
            $formatted = $contactType === ContactType::Uk ? $contact->formatUk() : $contact->formatCouncil();
            if ($text !== '') {
                $text .= "\n---\n";
            }
            $text .= $formatted;
        }

        // Обрезка при превышении лимита Max API (4000 символов)
        if (mb_strlen($text) > self::MAX_MESSAGE_LENGTH) {
            $suffix = "\n...текст сокращён";
            $text = mb_substr($text, 0, self::MAX_MESSAGE_LENGTH - mb_strlen($suffix)) . $suffix;
        }

        return $text;
    }
}
