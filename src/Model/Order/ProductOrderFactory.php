<?php
declare(strict_types=1);

namespace Dinargab\Homework15\Model\Order;

use Dinargab\Homework15\Service\Observers\PushNotificationObserver;
use Dinargab\Homework15\Service\Observers\SmsNotificationObserver;

class ProductOrderFactory
{
    public function __construct(
        private SmsNotificationObserver  $smsNotificationObserver,
        private PushNotificationObserver $pushNotificationObserver
    )
    {

    }

    public function create()
    {
        $productOrder = new ProductOrder();
        $productOrder->attach($this->smsNotificationObserver);
        $productOrder->attach($this->pushNotificationObserver);
        return $productOrder;
    }

}