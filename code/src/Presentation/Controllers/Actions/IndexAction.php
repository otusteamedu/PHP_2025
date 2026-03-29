<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Actions;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;
use App\Domain\Interfaces\ActionInterface;

class IndexAction implements ActionInterface
{
    public function supports(Request $request): bool
    {
        return $request->isGet() && $request->getPath() === '/';
    }

    public function handle(Request $request): Response
    {
        return Response::success([
            'name' => 'API интернет-ресторана фаст-фуда',
            'version' => '1.0.0',
            'endpoints' => [
                'GET /menu' => 'Получить меню ресторана',
                'POST /order' => 'Создать заказ',
                'GET /order/{id}' => 'Получить информацию о заказе',
                'GET /order/{id}/history' => 'Получить историю статусов заказа',
                'POST /order/{id}/cancel' => 'Отменить заказ',
            ],
            'example_order' => [
                'product_type' => 'burger',
                'additions' => ['lettuce', 'cheese', 'tomato'],
            ],
        ]);
    }
}
