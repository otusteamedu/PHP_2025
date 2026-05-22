<?php

declare(strict_types=1);

namespace App;

use App\Database\DBConnectionFactory;
use App\Exception\RequestNotFoundException;
use App\Http\JsonResponse;
use App\Queue\QueuePublisherFactory;
use App\Repository\RequestRepository;
use App\Routing\Router;
use InvalidArgumentException;
use Throwable;

/**
 * Приложение обработки HTTP-запросов
 */
final class Application
{
    /**
     * Запускает обработку текущего HTTP-запроса
     *
     * @return void
     */
    public function run(): void
    {
        $response = new JsonResponse();

        try {
            $repository = new RequestRepository((new DBConnectionFactory())->create());
            $publisher = (new QueuePublisherFactory())->create();

            $router = new Router($repository, $publisher);
            $router->handleRequest(
                $_SERVER['REQUEST_METHOD'] ?? 'GET',
                parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/',
            );
        } catch (InvalidArgumentException $exception) {
            $response->send(['error' => $exception->getMessage()], 400);
        } catch (RequestNotFoundException $exception) {
            $response->send(['error' => $exception->getMessage()], 404);
        } catch (Throwable $exception) {
            error_log((string) $exception);
            $response->send(['error' => 'Произошла ошибка при обработке запроса'], 500);
        }
    }
}
