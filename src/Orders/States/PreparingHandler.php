<?php declare(strict_types=1);

namespace Fastfood\Orders\States;

use Exception;
use Fastfood\Orders\Order;

class PreparingHandler extends OrderStateHandler
{

    /**
     * @throws Exception
     */
    public function process(Order $order): void
    {
        if ($order->getStatus() === 'preparing') {
            $this->waitForOrderCompletion();
            $order->setStatus('ready');
            $order->notify('order_ready');
        }

        $this->handle($order);
    }

    private function waitForOrderCompletion(): void
    {
        echo("\n\nОжидание завершения готовки заказа...\n");
        for ($i = 0; $i < 8; $i++) {
            sleep(1);
            echo ".";
        }
        echo "\n";
    }

}