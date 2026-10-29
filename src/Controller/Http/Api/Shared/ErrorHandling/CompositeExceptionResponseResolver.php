<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\Shared\ErrorHandling;

use App\Core\Http\ErrorHandler\ExceptionResponseResolverInterface;
use App\Core\Http\Message\Response;

class CompositeExceptionResponseResolver implements ExceptionResponseResolverInterface
{
    /**
     * @param list<ExceptionResponseResolverInterface> $resolvers
     */
    public function __construct(
        private readonly array $resolvers,
    ) {
    }

    public function resolve(\Throwable $e): ?Response
    {
        foreach ($this->resolvers as $resolver) {
            $response = $resolver->resolve($e);

            if ($response !== null) {
                return $response;
            }
        }

        return null;
    }
}
