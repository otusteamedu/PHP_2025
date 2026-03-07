<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http\Chat;

use Otus\Queue\Application\UseCase\Chat\Store;
use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Domain\Exception\ValidationException;
use Otus\Queue\Infrastructure\Http\Request;
use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;

final readonly class StoreController
{
    /**
     * @param Store $useCase
     */
    public function __construct(
        private Store $useCase,
    ) {
    }

    /**
     * @param Request $request
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request): ResponseInterface
    {
        try {
            $message = new Message(
                author: $request->body['author'],
                text: $request->body['text'],
                createdAt: time(),
            );

            $this->useCase->handle($message);
        } catch (ValidationException $validationException) {
            return ResponseFactory::toJson(
                [
                    'errors' => $validationException->getErrors(),
                ],
                422,
            );
        }

        return ResponseFactory::toJson($message->toArray(), 201);
    }
}
