<?php
namespace App\Controllers;

use App\Repositories\OrderRepository;
use App\Services\RabbitMQService;
use App\Views\View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OrderController
{
    public function __construct(
        private OrderRepository $orders,
        private RabbitMQService $mq,
        private View $view
    ) {}

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];
        $email = trim((string)($data['email'] ?? ''));
        $dateFrom = (string)($data['date_from'] ?? '');
        $dateTo = (string)($data['date_to'] ?? '');

        // Простая валидация
        if ($email === '' || $dateFrom === '' || $dateTo === '') {
            $response->getBody()->write('Неверные данные формы');
            return $response->withStatus(400);
        }

        $id = $this->orders->create($email, $dateFrom, $dateTo);

        $this->mq->publish(RabbitMQService::ORDERS_QUEUE, [
            'order_id' => $id,
            'email' => $email,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'created_at' => time(),
        ]);

        $html = $this->view->render('order_success', [
            'id' => $id,
            'email' => $email,
        ]);
        $response->getBody()->write($html);
        return $response;
    }
}
