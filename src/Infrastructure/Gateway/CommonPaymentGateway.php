<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Infrastructure\Gateway;

use Exception;
use Kamalo\BurgersShop\Application\Gateway\PaymentGatewayInterface;
use Kamalo\BurgersShop\Domain\Entity\Order;

class CommonPaymentGateway implements PaymentGatewayInterface
{
    public function pay(Order $order): void
    {
        sleep(2);
        $bankId= random_int(10_000, 99_000);

        if($bankId % 10 <= 2){
            throw new Exception('Ошибка при оплате заказа №' . $order->getId());
        }
    }
}