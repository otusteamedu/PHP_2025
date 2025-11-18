<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\EventListener\Exception;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Webmozart\Assert\InvalidArgumentException;

class ExceptionListener
{
    public const MIME_JSON = 'application/json';

    public function __construct(private readonly ContainerBagInterface $containerBag)
    {
    }

    #[AsEventListener(priority: 190)]
    public function onKernelException(ExceptionEvent $event): void
    {
        // Получаем MIME тип из заголовка Accept
        $acceptHeader = $event->getRequest()->headers->get('Accept');

        if (self::MIME_JSON === $acceptHeader) {
            $exception = $event->getThrowable();
            $response = new JsonResponse();
            if (!$this->isStatusCodeNotValid($exception->getCode())) {
                $response->setStatusCode($exception->getCode());
            }
            $response->setData($this->exceptionToArray($exception));
            // HttpException содержит информацию о заголовках и статусе, используем это
            if ($exception instanceof HttpExceptionInterface) {
                $response->setStatusCode($exception->getStatusCode());
                $response->headers->add($exception->getHeaders());
            }
            if ($exception instanceof InvalidArgumentException) {
                $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $event->setResponse($response);
        }
    }

    /**
     * @return array<string,string>\
     */
    public function exceptionToArray(\Throwable $exception): array
    {
        $data = ['message' => $exception->getMessage()];
        if ($this->containerBag->get('kernel.debug')) {
            $data = array_merge(
                $data,
                [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                ]
            );
        }

        return $data;
    }

    private function isStatusCodeNotValid(int $statusCode): bool
    {
        return $statusCode < 100 || $statusCode >= 600;
    }
}
