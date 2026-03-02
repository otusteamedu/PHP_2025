<?php

declare(strict_types=1);

namespace Otus\Queue\Domain\Validator;

use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Domain\Exception\ValidationException;

final readonly class MessageValidator
{
    /**
     * @param Message $message
     */
    public function __construct(
        private Message $message,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function validate(): void
    {
        $errors = [];

        if (trim($this->message->author) === '') {
            $errors['author'] = 'Author is required';
        }

        if (trim($this->message->text) === '') {
            $errors['text'] = 'Text is required';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }
}
