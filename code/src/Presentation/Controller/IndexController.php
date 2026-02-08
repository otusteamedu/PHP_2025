<?php

declare(strict_types=1);

namespace Ak\Hw\Presentation\Controller;

use Ak\Hw\Application\Service\OrderProcessingService;
use Psr\Container\ContainerInterface;

class IndexController
{
    private OrderProcessingService $orderProcessingService;

    public function __construct(private ContainerInterface $container)
    {
        $this->orderProcessingService = $this->container->get('OrderProcessingService');
    }

    public function handleRequest(): void
    {
        $viewData = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product'])) {
            $productNames = (array)$_POST['product'];
            $customIngredients = isset($_POST['custom_ingredients']) && !empty($_POST['custom_ingredients'])
                ? explode(',', $_POST['custom_ingredients'])
                : [];
            $notificationType = $_POST['notification_type'] ?? 'Push';

            $viewData = $this->orderProcessingService->processOrder(
                $productNames,
                $customIngredients,
                $notificationType
            );
        }
        $this->renderForm($viewData);
    }

    private function renderForm(array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../view/order_form.php';
    }
}
