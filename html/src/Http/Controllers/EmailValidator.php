<?php

declare(strict_types=1);

namespace Otus\Http\Controllers;

use Laminas\Diactoros\Response;
use Otus\Contracts\EmailValidatorServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class EmailValidator
{
    /**
     * @param EmailValidatorServiceInterface $emailValidator
     */
    public function __construct(
        protected EmailValidatorServiceInterface $emailValidator,
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $validate = false;
        $query = $request->getQueryParams();

        $email = $query['email'] ?? null;

        if (is_string($email)) {
            $validate = $this->emailValidator->handle($email);
        }

        $response = new Response();

        if ($validate === true) {
            $response
                ->getBody()
                ->write('OK');

            return $response;
        }

        $response
            ->getBody()
            ->write('Bad Request');

        return $response
            ->withStatus(400);
    }
}
