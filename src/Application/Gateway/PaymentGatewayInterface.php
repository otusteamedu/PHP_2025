<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\Gateway;

use Kamalo\BurgersShop\Domain\Entity\Order;

interface PaymentGatewayInterface
{
    // бесполезный класс который будет генерировать случайное число и при определенном значении выдавть ошибку оплаты
    // тем самым меняя статус заказа и вызывая событие OrderStatusChanged

    public function pay(Order $order): void;
}