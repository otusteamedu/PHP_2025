<?php

namespace App\Internal;

use App\Controller\CreateEvent;
use App\Controller\GetEvent;
use App\Dto\CreateEventEntryDto;
use App\Dto\ResponseDto;
use App\Service\EventService;
use App\Service\RabbitService;
use App\Service\RedisService;
use App\Service\RequestBodyService;
use Throwable;

class App
{
    public function run(): string
    {
        if ($this->checkAuth() === false) {
            $responseDto = new ResponseDto(
                message: 'Unauthorized',
                code: 401,
            );

            http_response_code($responseDto->code);

            return $responseDto->getResponseJson();
        }

        try {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $method = $_SERVER['REQUEST_METHOD'];

            switch ($method) {
                case 'GET':
                    if (preg_match('/^\/event\/([^\/]+)$/', $uri, $matches)) {
                        $responseDto = $this->processGetEventAction($matches[1]);

                        break;
                    }

                    break;
                case 'POST':
                    $responseDto = match ($uri) {
                        '/event' => $this->processCreateEventAction(),
                        default => new ResponseDto(
                            message: 'Not found',
                            code: 404,
                        ),
                    };

                    break;
                default:
                    $responseDto = new ResponseDto(
                        message: 'Not found',
                        code: 404,
                    );
            }
        } catch (Throwable) {
            $responseDto = new ResponseDto(
                message: 'Internal server error',
                code: 500,
            );
        }

        http_response_code($responseDto->code);

        return $responseDto->getResponseJson();
    }

    private function processGetEventAction(string $eventId): ResponseDto
    {
        $eventService = new EventService(new RabbitService(), new RedisService());
        $responseData = (new GetEvent($eventService))->process($eventId);

        return new ResponseDto(
            data: $responseData,
        );
    }

    private function processCreateEventAction(): ResponseDto
    {
        $eventService = new EventService(new RabbitService(), new RedisService());
        try {
            $requestBody = RequestBodyService::getDecodedJsonBody();
            $entryDto = new CreateEventEntryDto(
                message: $requestBody['message'],
            );
        } catch (Throwable) {
            return new ResponseDto(
                message: 'Invalid request body',
                code: 400,
            );
        }

        $id = (new CreateEvent($eventService))->process($entryDto);

        return new ResponseDto(
            message: 'Your request in progress',
            data: [
                'id' => $id,
            ],
        );
    }

    private function checkAuth(): bool
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        return !empty($authHeader) && $authHeader === getenv('AUTH_TOKEN');
    }
}
